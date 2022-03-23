<?php
namespace anthonypauwels\Metadata;

/**
 * Class MetadataGenerator
 *
 * @package anthonypauwels\Metadata
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class MetadataGenerator
{
    /** @var string Prefix for Twitter tags */
    const TWITTER_PREFIX = 'twitter:';

    /** @var string Prefix for Opengraph tags */
    const OPENGRAPH_PREFIX = 'og:';

    /** @var string Prefix for Facebook tags */
    const FACEBOOK_PREFIX = 'fb:';

    /** @var MetadataGenerator|null */
    protected static ?MetadataGenerator $instance = null;

    /** @var array Default meta tags, commonly used by search engine */
    protected array $meta = [];

    /** @var array Twitter meta tags, used only by Twitter */
    protected array $twitter = [];

    /** @var array Opengraph meta tags, used by Facebook, Instagram, Whatsapp, Discord, etc */
    protected array $opengraph = [];

    /** @var string */
    protected string $prefixUrl = '';

    /**
     * Return the MetadataGenerator instance
     *
     * @return MetadataGenerator
     */
    public static function getInstance():MetadataGenerator
    {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Set the prefix URL used by image and url tags
     *
     * @param string $prefix_url
     * @return $this
     */
    public function setPrefixUrl(string $prefix_url): MetadataGenerator
    {
        $this->prefixUrl = $prefix_url;

        return $this;
    }

    /**
     * Set an array of tags
     *
     * @param array $tags
     * @return $this
     */
    public function setTags(array $tags): MetadataGenerator
    {
        foreach ( $tags as $key => $values ) {
            if ( !is_integer( $key ) ) {
                if ( method_exists( $this, $key ) ) {
                    if ( !is_array( $values ) ) {
                        $values = [ $values ];
                    }

                    $this->{$key}( ...$values );
                } else {
                    $this->meta( $key, $values );
                }

                continue;
            }

            if ( $key & MetadataProtocol::META ) {
                foreach ( $values as $k => $v ) {
                    switch ( $k ) {
                        case 'title':
                        case 'description':
                        case 'url':
                        case 'robots':
                            $this->{$k}( $v, MetadataProtocol::META );
                            break;

                        default:
                            $this->meta( $k, $v );
                    }
                }
            }

            if ( $key & MetadataProtocol::TWITTER ) {
                foreach ( $values as $k => $v ) {
                    switch ( $k ) {
                        case 'title':
                        case 'description':
                        case 'url':
                            $this->{$k}( $v, MetadataProtocol::TWITTER );
                            break;

                        case 'image':
                            $v = array_merge( $v, [ MetadataProtocol::TWITTER ] );
                            $this->image( ...$v );
                            break;

                        case 'card':
                            $this->twitterCard( $v );
                            break;

                        default:
                            $this->twitter( $k, $v );
                    }
                }
            }

            if ( $key & MetadataProtocol::OPENGRAPH ) {
                foreach ( $values as $k => $v ) {
                    switch ( $k ) {
                        case 'title':
                        case 'description':
                        case 'url':
                            $this->{$k}( $v, MetadataProtocol::OPENGRAPH );
                            break;

                        case 'image':
                            $v = array_merge( $v, [ MetadataProtocol::OPENGRAPH ] );
                            $this->image( ...$v );
                            break;

                        case 'type':
                            $this->type( $v );
                            break;

                        default:
                            $this->opengraph( $k, $v );
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Set the page title in meta tags
     *
     * @param string $title
     * @param int $flags
     * @return $this
     */
    public function title(string $title, int $flags = MetadataProtocol::ALL): MetadataGenerator
    {
        if ( $this->ifMeta( $flags ) ) {
            $this->meta( 'title', $title );
        }

        if ( $this->ifTwitter( $flags ) ) {
            $this->twitter( 'title', $title );
        }

        if ( $this->ifOpengraph( $flags ) ) {
            $this->opengraph( 'title', $title );
        }

        return $this;
    }

    /**
     * Set the page description in meta tags
     *
     * @param string $description
     * @param int $flags
     * @return $this
     */
    public function description(string $description, int $flags = MetadataProtocol::ALL): MetadataGenerator
    {
        if ( $this->ifMeta( $flags ) ) {
            $this->meta( 'description', $description );
        }

        if ( $this->ifTwitter( $flags ) ) {
            $this->twitter( 'description', $description );
        }

        if ( $this->ifOpengraph( $flags ) ) {
            $this->opengraph( 'description', $description );
        }

        return $this;
    }

    /**
     * Set the image for the page used for cards inside app; Can be used to set image options like size or mimetype
     *
     * @param string $url
     * @param array $options
     * @param int $flags
     * @return $this
     */
    public function image(string $url, array $options = [], int $flags = MetadataProtocol::TWITTER | MetadataProtocol::OPENGRAPH): MetadataGenerator
    {
        $meta = [ 'width' => null, 'height' => null, 'type' => null, 'alt' => null, 'secure' => null];

        $options = array_intersect_key( $options, array_flip( array_keys( $meta ) ) );
        $options = array_filter( array_merge( $meta, $options ) );

        switch ( $options['type'] ) {
            case 'jpeg' :
            case 'jpg' :
            case 'png' :
            case 'gif' :
                $options['type'] = 'image/' . strtolower( $options['type'] );
                break;
        }

        if ( $this->ifTwitter( $flags ) ) {
            $this->twitter( 'image', $this->getPrefixUrl() . $url );

            foreach ( $options as $key => $value ) {
                $this->twitter( 'image:' . $key, $value );
            }
        }

        if ( $this->ifOpengraph( $flags ) ) {
            $this->opengraph( 'image', $this->getPrefixUrl() . $url );

            foreach ( $options as $key => $value ) {
                $this->opengraph( 'image:' . $key, $value );
            }
        }

        return $this;
    }

    /**
     * Set the page URL; by default, it's the current URL
     *
     * @param string|null $url
     * @param int $flags
     * @return $this
     */
    public function url(?string $url = null, int $flags = MetadataProtocol::TWITTER | MetadataProtocol::OPENGRAPH): MetadataGenerator
    {
        if ( $url === null ) { // no URL has been set, so we fetch the current url
            $url = $_SERVER[ 'REQUEST_URI' ];
        }

        if ( $this->ifTwitter( $flags ) ) {
            $this->twitter( 'url', $this->getPrefixUrl() . $url );
        }

        if ( $this->ifOpengraph( $flags ) ) {
            $this->opengraph( 'url', $this->getPrefixUrl() . $url );
        }

        return $this;
    }

    /**
     * Set the content og type
     *
     * @param string $type
     * @param array $options
     * @return $this
     */
    public function type(string $type = 'website', array $options = []): MetadataGenerator
    {
        $prefix = self::OPENGRAPH_PREFIX;
        $authorized_keys = [];

        switch ( $type ) {
            case 'music.song' :
                $prefix = 'music:';
                $this->opengraph( 'type', 'music.song' );
                $authorized_keys = ['duration', 'album', 'album:disc', 'album:track', 'musician'];
                break;

            case 'music.album' :
                $prefix = 'music:';
                $this->opengraph( 'type', 'music.album' );
                $authorized_keys = ['song', 'song:disc', 'song:track', 'musician', 'release_date'];
                break;

            case 'music.playlist' :
                $prefix = 'music:';
                $this->opengraph( 'type', 'music.playlist' );
                $authorized_keys = ['song', 'song:disc', 'song:track', 'creator'];
                break;

            case 'music.radio_station' :
                $prefix = 'music:';
                $this->opengraph( 'type', 'music.radio_station' );
                $authorized_keys = ['creator'];
                break;

            case 'music' :
                $this->opengraph( 'type', 'music' );
                break;

            case 'video.tv_show' :
            case 'video.other' :
            case 'video.movie' :
                $prefix = 'video:';
                $this->opengraph( 'type', 'video' );
                $authorized_keys = ['actor', 'actor:role', 'director', 'writer', 'duration', 'release_date', 'tag'];
                break;

            case 'video.episode' :
                $prefix = 'video:';
                $this->opengraph( 'type', 'video' );
                $authorized_keys = ['actor', 'actor:role', 'director', 'writer', 'duration', 'release_date', 'tag', 'series'];
                break;

            case 'video' :
                $this->opengraph( 'type', 'video' );
                break;

            case 'article' :
                $prefix = 'article:';
                $this->opengraph( 'type', 'article' );
                $authorized_keys = ['published_time', 'modified_time', 'expiration_time', 'author', 'section', 'tag'];
                break;

            case 'book' :
                $prefix = 'book:';
                $this->opengraph( 'type', 'book' );
                $authorized_keys = ['author', 'isbn', 'release_date', 'tag'];
                break;

            case 'profile' :
                $prefix = 'profile:';
                $this->opengraph( 'type', 'profile' );
                $authorized_keys = ['first_name', 'last_name', 'username', 'gender'];
                break;

            default :
                $this->opengraph( 'type', 'website' );
        }

        foreach ( $options as $key => $value ) {
            if ( in_array( $key, $authorized_keys ) ) {
                $this->opengraph( $key, $value, $prefix );
            }
        }

        return $this;
    }

    /**
     * Set the author's name
     *
     * @param string $author
     * @return $this
     */
    public function author(string $author): MetadataGenerator
    {
        $this->meta( 'author', $author );

        return $this;
    }

    /**
     * Set the Twitter card format
     *
     * @param string $card_type
     * @return $this
     */
    public function twitterCard(string $card_type = 'summary'): MetadataGenerator
    {
        if ( !in_array( $card_type, [ 'summary', 'summary_large_image', 'app', 'player' ] ) ) {
            $card_type = 'summary';
        }

        $this->twitter( 'card', $card_type );

        return $this;
    }

    /**
     * Set the Twitter website profile
     *
     * @param string $twitter_site
     * @return $this
     */
    public function twitterSite(string $twitter_site): MetadataGenerator
    {
        $this->twitter( 'site', $twitter_site );

        return $this;
    }

    /**
     * Set the twitter author profile
     *
     * @param string $twitter_creator
     * @return $this
     */
    public function twitterCreator(string $twitter_creator): MetadataGenerator
    {
        $this->twitter( 'creator', $twitter_creator );

        return $this;
    }

    /**
     * Set the facebook app_id
     *
     * @param string $app_id
     * @return $this
     */
    public function fbAppId(string $app_id): MetadataGenerator
    {
        $this->opengraph( 'app_id', $app_id, self::FACEBOOK_PREFIX );

        return $this;
    }

    /**
     * Set the facebook admins tag
     *
     * @param string $admins
     * @return $this
     */
    public function fbAdmins(string $admins): MetadataGenerator
    {
        $this->opengraph( 'admins', $admins, self::FACEBOOK_PREFIX );

        return $this;
    }

    /**
     * Set the og:site_name tag
     *
     * @param string $site_name
     * @return $this
     */
    public function siteName(string $site_name): MetadataGenerator
    {
        $this->opengraph( 'site_name', $site_name );

        return $this;
    }

    /**
     * Disable (or enable if $value is true) the pinterest-rich-pin
     *
     * @param bool $value
     * @return $this
     */
    public function disablePinterestRichPin(bool $value = true): MetadataGenerator
    {
        $this->meta( 'pinterest-rich-pin', !$value ? 'false' : 'true' );

        return $this;
    }

    /**
     * Set the robot meta tag
     *
     * @param mixed ...$values
     * @return MetadataGenerator
     */
    public function robots(...$values): MetadataGenerator
    {
        if ( isset( $values[0] ) && is_array( $values[0] ) ) {
            $values = $values[0];
        }

        $authorized_values = [ 'all', 'noindex', 'nofollow', 'none', 'noarchive', 'nosnippet', 'notranslate', 'noimageindex', ];

        $this->meta('robots', implode(', ', array_intersect( $values, $authorized_values ) ) );

        return $this;
    }

    /**
     * Set a meta tag with given key and given value
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function meta(string $name, string $value): MetadataGenerator
    {
        $this->meta[ $name ] = compact( 'name', 'value' );

        return $this;
    }

    /**
     * Set a meta tag for Twitter
     *
     * @param string $name
     * @param string $value
     * @param string $prefix
     * @return $this
     */
    public function twitter(string $name, string $value, string $prefix = self::TWITTER_PREFIX): MetadataGenerator
    {
        $this->twitter[ $name ] = compact( 'name', 'value', 'prefix' );

        return $this;
    }

    /**
     * Set a meta tag for Opengraph
     *
     * @param string $name
     * @param string $value
     * @param string $prefix
     * @return $this
     */
    public function opengraph(string $name, string $value, string $prefix = self::OPENGRAPH_PREFIX): MetadataGenerator
    {
        $this->opengraph[ $name ] = compact( 'name', 'value', 'prefix' );

        return $this;
    }

    /**
     * Generate the HTML code of meta tags
     *
     * @param int $flags Determine the type of meta to generate
     * @return string
     */
    public function toHtml(int $flags = MetadataProtocol::ALL):string
    {
        $buffer = '';

        if ( $this->ifMeta( $flags ) ) {
            foreach ( $this->meta as $tag ) {
                $buffer.= '<meta name="' . $tag[ 'name' ] . '" content="' . $tag[ 'value' ] . '">' . PHP_EOL;
            }
        }

        if ( $this->ifTwitter( $flags ) ) {
            foreach ( $this->twitter as $tag ) {
                $buffer.= '<meta name="' . $tag[ 'prefix' ] . $tag[ 'name' ] . '" content="' . $tag[ 'value' ] . '">' . PHP_EOL;
            }
        }

        if ( $this->ifOpengraph( $flags ) ) {
            foreach ( $this->opengraph as $tag ) {
                $buffer.= '<meta name="' . $tag[ 'prefix' ] . $tag[ 'name' ] . '" content="' . $tag[ 'value' ] . '">' . PHP_EOL;
            }
        }

        return $buffer;
    }

    /**
     * Print the generated HTML code
     *
     * @param int $flags  Determine the type of meta to generate
     */
    public function print(int $flags = MetadataProtocol::ALL):void
    {
        echo $this->toHtml( $flags );
    }

    /**
     * Return the HTML code of meta tags with default parameter
     *
     * @return string
     */
    public function __toString():string
    {
        return $this->toHtml();
    }

    /**
     * Conditional method to check the $flags
     *
     * @param int $flags
     * @return bool
     */
    protected function ifMeta(int $flags):bool
    {
        return $flags & MetadataProtocol::ALL || $flags & MetadataProtocol::META;
    }

    /**
     * Conditional method to check the $flags
     *
     * @param int $flags
     * @return bool
     */
    protected function ifTwitter(int $flags):bool
    {
        return $flags & MetadataProtocol::ALL || $flags & MetadataProtocol::TWITTER;
    }

    /**
     * Conditional method to check the $flags
     *
     * @param int $flags
     * @return bool
     */
    protected function ifOpengraph(int $flags):bool
    {
        return $flags & MetadataProtocol::ALL || $flags & MetadataProtocol::OPENGRAPH;
    }

    /**
     * Return the prefix URL, mostly the domain name
     *
     * @return string
     */
    protected function getPrefixUrl():string
    {
        if ( $this->prefixUrl !== '' ) {
            return $this->prefixUrl;
        }

        if ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] === 'on' ) {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }

        return $protocol . '://' . $_SERVER[ 'HTTP_HOST' ];
    }
}