<?php
namespace Anthonypauwels\Metadata;

/**
 * Class Metadata
 *
 * @package Anthonypauwels\Metadata
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class Metadata
{
    /** @var int All meta tags */
    const ALL = 1;

    /** @var int Only common meta tags */
    const META = 2;

    /** @var int Only Twitter meta tags */
    const TWITTER = 4;

    /** @var int Only Opengraph meta tags */
    const OPENGRAPH = 8;

    /** @var string Prefix for Twitter tags */
    const TWITTER_PREFIX = 'twitter:';

    /** @var string Prefix for Opengraph tags */
    const OPENGRAPH_PREFIX = 'og:';

    /** @var string Prefix for Facebook tags */
    const FACEBOOK_PREFIX = 'fb:';

    /** @var Metadata|null */
    protected static ?Metadata $instance = null;

    /** @var array Default meta tags, commonly used by search engine */
    protected array $meta = [];

    /** @var array Twitter meta tags, used only by Twitter */
    protected array $twitter = [];

    /** @var array Opengraph meta tags, used by Facebook, Instagram, Whatsapp, Discord, etc */
    protected array $opengraph = [];

    /**
     * Return the Metadata instance
     *
     * @return Metadata
     */
    public static function getInstance():Metadata
    {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Set an array of tags
     *
     * @param array $tags
     * @return $this
     */
    public function setTags(array $tags): Metadata
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

            if ( $this->ifMeta( $key ) ) {
                foreach ( $values as $k => $v ) {
                    switch ( $k ) {
                        case 'title':
                        case 'description':
                        case 'url':
                        case 'robots':
                            $this->{$k}( $v, self::META );
                            break;

                        default:
                            $this->meta( $k, $v );
                    }
                }
            }

            if ( $this->ifTwitter( $key ) ) {
                foreach ( $values as $k => $v ) {
                    switch ( $k ) {
                        case 'title':
                        case 'description':
                        case 'url':
                            $this->{$k}( $v, self::TWITTER );
                            break;

                        case 'image':
                            $v = array_merge( $v, [ self::TWITTER ] );
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

            if ( $this->ifOpengraph( $key ) ) {
                foreach ( $values as $k => $v ) {
                    switch ( $k ) {
                        case 'title':
                        case 'description':
                        case 'url':
                            $this->{$k}( $v, self::OPENGRAPH );
                            break;

                        case 'image':
                            $v = array_merge( $v, [ self::OPENGRAPH ] );
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
    public function title(string $title, int $flags = self::ALL): Metadata
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
    public function description(string $description, int $flags = self::ALL): Metadata
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
    public function image(string $url, array $options = [], int $flags = self::TWITTER | self::OPENGRAPH): Metadata
    {
        $meta = [ 'width' => null, 'height' => null, 'type' => null, 'alt' => null];

        $options = array_intersect_key( $options, array_flip( array_keys( $meta ) ) );
        $options = array_filter( array_merge( $meta, $options ) );

        if ( isset( $options['type'] ) ) {
            switch ( $options['type'] ) {
                case 'jpeg' :
                case 'jpg' :
                case 'png' :
                case 'gif' :
                    $options['type'] = 'image/' . strtolower( $options['type'] );
                    break;
            }
        }

        if ( $this->ifTwitter( $flags ) ) {
            $this->twitter( 'image', $url );

            foreach ( $options as $key => $value ) {
                $this->twitter( 'image:' . $key, $value );
            }
        }

        if ( $this->ifOpengraph( $flags ) ) {
            $this->opengraph( 'image', $url );

            foreach ( $options as $key => $value ) {
                $this->opengraph( 'image:' . $key, $value );
            }
        }

        return $this;
    }

    /**
     * Shortcut to image() method for Twitter Card
     *
     * @param string $url
     * @param array $options
     * @return $this
     */
    public function twitterImage(string $url, array $options = []): Metadata
    {
        $this->image( $url, $options, self::TWITTER );

        return $this;
    }

    /**
     * Shortcut to image() method for Opengraph
     *
     * @param string $url
     * @param array $options
     * @return $this
     */
    public function opengraphImage(string $url, array $options = []): Metadata
    {
        $this->image( $url, $options, self::OPENGRAPH );

        return $this;
    }

    /**
     * Set the page URL
     *
     * @param string $url
     * @param int $flags
     * @return $this
     */
    public function url(string $url, int $flags = self::TWITTER | self::OPENGRAPH): Metadata
    {
        if ( $this->ifTwitter( $flags ) ) {
            $this->twitter( 'url', $url );
        }

        if ( $this->ifOpengraph( $flags ) ) {
            $this->opengraph( 'url', $url );
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
    public function type(string $type = 'website', array $options = []): Metadata
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
    public function author(string $author): Metadata
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
    public function twitterCard(string $card_type = 'summary'): Metadata
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
    public function twitterSite(string $twitter_site): Metadata
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
    public function twitterCreator(string $twitter_creator): Metadata
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
    public function fbAppId(string $app_id): Metadata
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
    public function fbAdmins(string $admins): Metadata
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
    public function siteName(string $site_name): Metadata
    {
        $this->opengraph( 'site_name', $site_name );

        return $this;
    }

    /**
     * Set the og:locale tag
     *
     * @param string $locale
     * @return $this
     */
    public function locale(string $locale): Metadata
    {
        $this->opengraph( 'locale', $locale );

        return $this;
    }

    /**
     * Disable (or enable if $value is true) the pinterest-rich-pin
     *
     * @param bool $value
     * @return $this
     */
    public function disablePinterestRichPin(bool $value = true): Metadata
    {
        $this->meta( 'pinterest-rich-pin', $value ? 'false' : 'true' );

        return $this;
    }

    /**
     * Set the robot meta tag
     *
     * @param mixed ...$values
     * @return Metadata
     */
    public function robots(...$values): Metadata
    {
        if ( isset( $values[0] ) && is_array( $values[0] ) ) {
            $values = $values[0];
        }

        $this->meta('robots', implode(', ', $values ) );

        return $this;
    }

    /**
     * Set a meta tag with given key and given value
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function meta(string $name, string $value): Metadata
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
    public function twitter(string $name, string $value, string $prefix = self::TWITTER_PREFIX): Metadata
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
    public function opengraph(string $name, string $value, string $prefix = self::OPENGRAPH_PREFIX): Metadata
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
    public function toHtml(int $flags = self::ALL):string
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
    public function print(int $flags = self::ALL):void
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
        return $flags & self::ALL || $flags & self::META;
    }

    /**
     * Conditional method to check the $flags
     *
     * @param int $flags
     * @return bool
     */
    protected function ifTwitter(int $flags):bool
    {
        return $flags & self::ALL || $flags & self::TWITTER;
    }

    /**
     * Conditional method to check the $flags
     *
     * @param int $flags
     * @return bool
     */
    protected function ifOpengraph(int $flags):bool
    {
        return $flags & self::ALL || $flags & self::OPENGRAPH;
    }
}
