<?php
/**
 * Contains the IDX_Plus_Common class.
 */

/**
 * Includes commonly used methods.
 */
class Bon_IDX_Helper {
    
    /**
     * Is the current page (or passed data array) a listing?
     * @param boolean $idx array of listing/search data
     * @return boolean It's a listing (true) or a search (false)
     */
    function is_listing($idx = false) {
        global $wp;
        if (empty($idx) && !empty($wp->wp_idx_content)) {
            $idx = $wp->wp_idx_content;
        }
        if (empty($idx)) {
            return false;
        }
        
        return (!empty($idx['mls']) || (isset($idx['type']) && $idx['type'] === 'listing'));
    }
    
    /**
     * Get a global variable
     * Content is stored in the `idx_plus_content` sub-array
     * @param string|boolean The key of the data. If you want to check all globals, leave empty.
     * @param string If you want the value from a sub-array, pass the key of that data.
     * @return mixed The data stored in the global
     * @version 2.0.41
     */
    function get_global($key = false, $subkey = false) {
        
        if (empty($key) && !empty($GLOBALS['wp_idx_content']) && !empty($subkey)) {
            foreach (array(
                'secondary',
                'supplemental',
                'schools'
            ) as $k) {
                $data = $this->get_global($k, $subkey);
                if ($data) {
                    return $data;
                }
            }
        }
        
        $data = !empty($GLOBALS['wp_idx_content']["{$key}"]) ? $GLOBALS['wp_idx_content']["{$key}"] : false;
        if ($data && is_array($data) && !empty($subkey) && is_string($subkey)) {
            $lcdata = array_change_key_case($data);
            $data   = false;
            if (isset($lcdata[strtolower($subkey)])) {
                $data = $this->trim($lcdata[strtolower($subkey)]);
            }
        }
        
        return !empty($data) ? $data : false;
    }
    
    /**
     * Set a global variable
     * Content is stored in the `idx_plus_content` sub-array
     * @param string $key The key of the data to store
     * @param string $val The value of the data to store
     * @return mixed The data stored in the global
     */
    function set_global($key, $val) {
        $GLOBALS['wp_idx_content']["{$key}"] = $val;
    }
    
    /**
     * Take a string and wrap the numbers in a span with the class passed
     * @param string $content The string of content
     * @param string $class The class to add to the span
     * @return string The string with numbers wrapped in a span
     */
    function wrap_numbers_in_span($content, $class = '') {
        if (!empty($class)) {
            $class = ' class="' . $class . '"';
        }
        // Either a number_format number, plain number, or n/a should be wrapped in spans
        $content = preg_replace('/(.*?)([\d]+,[\d]+|\d+|n\/a)(.*?)/im', '$1<span' . $class . '>$2</span>$3', $content);
        return $content;
    }
    
    /**
     * Makes it easy to preg_match without repeating logic.
     * @param string $pattern Regex pattern
     * @param string $subject Content to match against
     * @param integer $group The number of the regex group that you want (normally 1)
     * @param string $backup If there are no matches, use this instead
     * @return string The matched result or the backup, depending of if matches exist
     */
    function match($pattern, $subject, $group = 1, $backup = '') {
        
        if (!is_int($group)) {
            return false;
        }
        
        preg_match($pattern, $subject, $match);
        
        if (isset($match[$group])) {
            return $this->trim($match[$group]);
        }
        
        return $backup;
    }
    
    /**
     * 'Spins' the content provided in "Spintax"
     *
     * Spinning content allows you to create SEO-friendly variations of articles. The idea is to avoid content being seen as duplicate content by search engines.
     *
     * Spintax example:
     *
     * This {sentence|phrase|short paragraph} is in spintax format.
     *
     * The above example would yield the following three variations:
     *
     * This sentence is in spintax format.
     * This phrase is in spintax format.
     * This short paragraph is in spintax format.
     *
     * By default, spins are the same on a per page basis; refreshing will not change text on the same URL. If you refresh a page, the content will not change. On different pages, however,
     * it will show different spin results. You can turn this off using `add_filter('bon_idx_predictable_spins', '__return_false');`
     *
     *
     * @filter idx_plus_predictable_spins
     * @param string $text text to spin
     * @return srtring Spun text (or not spun, if no spintax matches)
     * @version 2.0.41
     */
    function spin($text, $counter = 1) {
        
        if (!is_string($text)) {
            return $text;
        }
        
        $text = do_shortcode($text);
        
        if (apply_filters('bon_idx_predictable_spins', true)) {
            mt_srand(crc32($_SERVER['SERVER_NAME'] . '/' . $_SERVER['REQUEST_URI'] . $counter++));
        } else {
            mt_srand();
        }
        
        preg_match('/\{(.+?)\}/is', $text, $m);
        if (empty($m))
            return $text;
        
        $t = $m[1];
        
        if (strpos($t, '{') !== false) {
            $t = substr($t, strrpos($t, '{') + 1);
        }
        
        $parts = explode("|", $t);
        $text  = preg_replace("/\{" . preg_quote($t) . "\}/is", $parts[mt_rand(0, count($parts) - 1)], $text, 1);
        
        mt_srand(); // Make random random again
        
        $counter++;
        return $this->spin($text, $counter);
    }
    
    /**
     * Whether script debugging is enabled.
     * @return boolean [description]
     */
    function is_script_debug() {
        return ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) && (!isset($_GET['scriptdebug']) || (isset($_GET['scriptdebug']) && !empty($_GET['scriptdebug']))));
    }
    /**
     * QueryPath converts some whitespace to the HTML entity values. This replaces that whitespace with nothing.
     * @param string $content Content to strip
     * @param string $newlines Strip newlines and tabs as well?
     * @return string Stripped content
     */
    function strip_spaces($content, $newlines = false) {
        $array = $newlines ? array(
            '&nbsp;',
            '&#13;',
            "\n",
            "\t"
        ) : array(
            '&nbsp;',
            '&#13;'
        );
        return str_replace($array, ' ', $content);
    }
    
    /**
     * Trim a string, array, or object of leading or trailing whitespace
     * @uses rtrim()
     * @uses trim()
     * @param string|array|object $content Content to trim
     * @return string|array|object Trimmed content
     */
    function trim($content) {
        if (is_array($content) || is_object($content)) {
            foreach ($content as &$value) {
                $value = $this->trim($value);
            }
            return $content;
        }
        // The str_replace is for Query Path, which can convert whitespace to &#13;
        return trim(rtrim($this->strip_spaces($content)));
    }
    
    /**
     * Make inputs work with written shortcodes by HTML escaping shortcodes
     * @param string $content Content to escape
     * @return string escaped shortcode and esc_html()'d content
     */
    function sanitize_shortcode($content) {
        $content = str_replace('[', '&#91;', $content);
        $content = str_replace(']', '&#93;', $content);
        $content = esc_html($content);
        return $content;
    }
    
    
    /**
     * Turn a string of a number into a float.
     *
     * This works to convert "$1,000.34" to 1000.34, for example. Or "4 baths" to 4
     *
     * @uses $this->trim()
     * @uses strip_tags()
     * @uses floatval()
     * @param string|array $number Number
     * @return float The number
     * @version 2.0.41
     */
    function raw_number($number) {
        if (is_array($number)) {
            $number = array_map(array(
                'Bon_IDX_Helper',
                'raw_number'
            ), $number);
        }
        
        $number = floatval(preg_replace('/[^0-9.]/ism', '', $this->trim($number)));
        return $number;
    }
    
    /**
     * Get an array of all street abbreviations
     *
     * @return array Array of street type endings
     * @filter idx_plus_street_suffixes
     * @version 2.0.52
     */
    function get_street_types() {
        $suffixes = array(
            'aly',
            'ext',
            'lcks',
            'rue',
            'anx',
            'exts',
            'ldg',
            'run',
            'arc',
            'fall',
            'loop',
            'shl',
            'ave',
            'fls',
            'mall',
            'shls',
            'byu',
            'fry',
            'mnr',
            'shr',
            'bch',
            'fld',
            'mnrs',
            'shrs',
            'bnd',
            'bend',
            'flds',
            'mdw',
            'skwy',
            'blf',
            'flt',
            'flat',
            'mdws',
            'spg',
            'blfs',
            'flts',
            'mews',
            'mews',
            'spgs',
            'btm',
            'frd',
            'ford',
            'ml',
            'mill',
            'spur',
            'blvd',
            'frds',
            'mls',
            'br',
            'frst',
            'msn',
            'sq',
            'square',
            'brg',
            'frg',
            'mtwy',
            'sqs',
            'brk',
            'frgs',
            'mt',
            'sta',
            'brks',
            'frk',
            'fork',
            'mtn',
            'stra',
            'bg',
            'burg',
            'frks',
            'mtns',
            'strm',
            'bgs',
            'ft' /* ,'fort' */ ,
            'nck',
            'neck',
            'st',
            'byp',
            'fwy',
            'orch',
            'sts',
            'cp',
            'camp',
            'gdn',
            'oval',
            'smt',
            'cyn',
            'gdns',
            'ovlk',
            'ter',
            'terrace',
            'cpe',
            'cape',
            'gtwy',
            'opas',
            'trwy',
            'cswy',
            'gln',
            'glen',
            'park',
            'trce',
            'ctr',
            'glns',
            'parks',
            'trak',
            'ctrs',
            'grn',
            'pkwy',
            'trfy',
            'chse',
            'grns',
            'pass',
            'trl',
            'cir',
            'grv',
            'psge',
            'trlr',
            'cirs',
            'grvs',
            'path',
            'path',
            'tunl',
            'clf',
            'hbr',
            'pike',
            'pike',
            'tpke',
            'clfs',
            'hbrs',
            'pne',
            'pine',
            'upas',
            'clb',
            'club',
            'hvn',
            'pnes',
            'un',
            'cmn',
            'hts',
            'pl',
            'uns',
            'cmns',
            'hwy',
            'pln',
            'vly',
            'cor',
            'hl',
            'hill',
            'plns',
            'vlys',
            'cors',
            'hls',
            'plz',
            'via',
            'crse',
            'holw',
            'pt',
            'vw',
            'ct',
            'inlt',
            'pte',
            'vws',
            'cts',
            'is',
            'pts',
            'vlg',
            'cv',
            'cove',
            'iss',
            'pond',
            'pond',
            'vlgs',
            'cvs',
            'isle',
            'isle',
            'prt',
            'port',
            'vl',
            'crk',
            'jct',
            'prts',
            'vis',
            'cres',
            'jcts',
            'pr',
            'walk',
            'crst',
            'ky',
            'key',
            'radl',
            'xing',
            'kys',
            'keys',
            'ramp',
            'ramp',
            'wall',
            'xrd',
            'knl',
            'rnch',
            'way',
            'xrds',
            'knls',
            'rpd',
            'ways',
            'curv',
            'lk',
            'lake',
            'rpds',
            'wls',
            'dl',
            'dale',
            'lks',
            'rst',
            'rest',
            'wl',
            'dm',
            'dam',
            'land',
            'rdg',
            'whf',
            'dv',
            'lndg',
            'rdgs',
            'rue',
            'run',
            'dr',
            'ln',
            'lane',
            'riv',
            'drs',
            'lgt',
            'rd',
            'est',
            'lgts',
            'rds',
            'ests',
            'lf',
            'loaf',
            'rte',
            'expy',
            'lck',
            'lock',
            'row',
            'circle',
            'court',
            'place',
            'street',
            'road',
            'drive',
            'avenue',
            'boulevard'
        );
        
        $direction_suffixes = array();
        /**
         * Add the directions for addresses like "Drive NE" and "Drive NW"
         */
        foreach ($suffixes as $suffix) {
            $direction_suffixes[] = $suffix . '_e';
            $direction_suffixes[] = $suffix . '_w';
            $direction_suffixes[] = $suffix . '_n';
            $direction_suffixes[] = $suffix . '_s';
            $direction_suffixes[] = $suffix . '_ne';
            $direction_suffixes[] = $suffix . '_nw';
            $direction_suffixes[] = $suffix . '_se';
            $direction_suffixes[] = $suffix . '_sw';
        }
        
        return apply_filters('bon_idx_street_suffixes', array_merge($direction_suffixes, $suffixes));
    }
    
    /**
     * Get an array of related street types (where one means the other)
     *
     * Example: St = Street
     *
     * @see $this->get_street_types()
     * @param string
     * @return array Array of endings that are related.
     * @since 2.0.42
     */
    function get_street_type_pairings($StreetType) {
        switch (strtolower($StreetType)) {
            case 'st':
            case 'street':
                $streettypes[] = 'St';
                $streettypes[] = 'Street';
                break;
            case 'dr':
            case 'drive':
                $streettypes[] = 'Dr';
                $streettypes[] = 'Drive';
                break;
            case 'bnd':
            case 'bend':
                $streettypes[] = 'Bend';
                $streettypes[] = 'Bnd';
                break;
            case 'pkwy':
            case 'parkway':
                $streettypes[] = 'Parkway';
                $streettypes[] = 'Pkwy';
                break;
            case 'ridge':
            case 'rdg':
                $streettypes[] = 'Ridge';
                $streettypes[] = 'Rdg';
                break;
            case 'square':
            case 'sq':
                $streettypes[] = 'Square';
                $streettypes[] = 'Sq';
                break;
            case 'rd':
            case 'road':
                $streettypes[] = 'Rd';
                $streettypes[] = 'Road';
                break;
            case 'ln':
            case 'lane':
                $streettypes[] = 'Ln';
                $streettypes[] = 'Lane';
                break;
            case 'ave':
            case 'avenue':
                $streettypes[] = 'Ave';
                $streettypes[] = 'Avenue';
                break;
            case 'cir':
            case 'circle':
                $streettypes[] = 'Cir';
                $streettypes[] = 'Circle';
                break;
            case 'ct':
            case 'court':
                $streettypes[] = 'Ct';
                $streettypes[] = 'Court';
                break;
            default:
                $streettypes[] = $StreetType;
                break;
        }
        return $streettypes;
    }
    
    /**
     * Return only the street name from an address
     *
     * Inspired by <a href="http://adamprescott.net/2012/02/29/simple-regular-expression-us-street-address-parser-in-c/">Adam Prescott</a>
     *
     * @param string $address Full address (214 Sunny St.)
     * @param boolean $all Return the regex match array, not just the street name
     * @return string|array If $all: Street name (Sunny St), else: array('HouseNumber', 'StreetPrefix', 'StreetName', 'StreetType', 'StreetSuffix', 'Apt');
     * @since 2.0.41
     * @version 2.0.42
     */
    function get_street_from_address($address, $all = false) {
        $streettypes = implode('|', $this->get_street_types());
        $regex       = '/^(?<HouseNumber>\d+)(?:\s+(?<StreetPrefix>TE|NW|HW|RD|E|MA|EI|NO|AU|SE|GR|OL|W|MM|OM|SW|ME|HA|JO|OV|S|OH|NE|K|N))?(?:\s+(?<StreetName>.*?))(?:(?:\s+(?<StreetType>' . $streettypes . '))(?:\s+(?<StreetSuffix>NW|E|SE|W|SW|S|NE|N))?(?:,?\s+(?<Apt>.*))?)?\.?$/ism';
        
        preg_match($regex, $address, $matches);
        
        if ($all) {
            return $matches;
        }
        
        if (isset($matches['StreetName']) && isset($matches['StreetType'])) {
            return $this->trim($matches['StreetPrefix'] . ' ' . $matches['StreetName'] . ' ' . $matches['StreetType']);
        }
    }
    
    /**
     * Turn a number into a number_format-ed number
     * @uses number_format()
     * @param mixed $number Number to number_format
     * @param boolean $make_raw Apply $this->raw_number() to the number
     * @return mixed|string If the number is under 1,000, it won't format the number (and will return the original content)
     * @version 2.0.41
     */
    function number_format($number, $make_raw = true) {
        if ($make_raw) {
            $number = $this->raw_number($number);
        }
        if ($number >= 1000) {
            return number_format($number);
        } else {
            return $number;
        }
    }
    
    /**
     * Add custom IDX+ capabilities to existing roles (like administrator, editor)
     * @param string $role Name of role
     * @return array Array of IDX+ capabilities for that role
     */
    function get_caps($role = 'administrator') {
        
        $editor_caps = array(
            'idx_plus_admin_panel',
            'idx_plus_edit_custom_content',
            'idx_plus_assign_leads',
            'idx_plus_add_lead_group',
            'idx_plus_view_lead_groups',
            'idx_plus_assign_lead_groups',
            'idx_plus_view_assigned_leads',
            'idx_plus_edit_assigned_leads'
        );
        
        if ($role === 'editor' || $role === 'agent') {
            return $editor_caps;
        }
        
        $all_caps = array(
            'idx_plus_edit_settings',
            'idx_plus_view_updates',
            'idx_plus_admin_panel',
            'idx_plus_edit_custom_content',
            'idx_plus_assign_leads',
            'idx_plus_add_lead_group',
            'idx_plus_view_lead_groups',
            'idx_plus_assign_lead_groups',
            'idx_plus_view_all_leads',
            'idx_plus_view_assigned_leads',
            'idx_plus_edit_all_leads',
            'idx_plus_edit_assigned_leads'
        );
        
        return $all_caps;
    }
    
    /**
     * Check whether an user has any IDX+ capabilities
     * @param array $caps Capability array
     * @return boolean Whether an user has any caps
     */
    public function current_user_can_any($caps) {
        
        if (is_string($caps)) {
            return current_user_can($caps) || current_user_can("idx_plus_full_access");
        }
        
        if (!is_array($caps)) {
            return false;
        }
        
        foreach ($caps as $cap) {
            if (current_user_can($cap))
                return true;
        }
        
        return current_user_can("idx_plus_full_access");
    }
    
    /**
     * Is the value empty?
     *
     * Allows you to pass a function instead of just a variable, like the empty() function insists upon (until PHP 5.5)
     *
     * @param mixed $value Check whether this is empty
     * @return boolean Empty or not?
     */
    public function is_empty($value = '') {
        $value = $this->trim($value);
        return (empty($value) || $value === 'false');
    }
    
  
    /**
     * Convert IDX+ price grouping plaintext into array
     *
     * Example: `Listings over $100,000` will be converted to `array('minprice' => 0, 'maxprice' => 100000, 'text' => "Listings over $100,000");`
     *
     * @param string $content Plain english price grouping with new lines for each list item
     * @return array Array of groupings
     */
    public function process_price_grouping($content = '') {
        if ($this->is_empty($content)) {
            $content = IDX_Plus::get_setting('price_grouping');
        }
        
        # $grouping = preg_replace('/[^A-Za-z0-9-\s]/ism', '', $grouping);
        
        $grouping = explode("\n", $content);
        
        $parsedGrouping = array();
        foreach ($grouping as $group) {
            
            if (preg_match('/(.*?)\s+?-\s+?(.*?)\r/xism', $group, $matches)) {
                $minprice = $matches[1];
                $maxprice = $matches[2];
            } elseif (preg_match('/(Less\ Th[ae]n|Under|Over|Above|Below|More\ Th[ae]n)\s+?(.*)\r?/xism', $group, $matches)) {
                if (strtolower($matches[1]) == 'under' || strtolower($matches[1]) == 'below' || strtolower($matches[1]) == 'less than' || strtolower($matches[1]) == 'less then') {
                    $minprice = null;
                    $maxprice = $matches[2];
                } else {
                    $maxprice = null;
                    $minprice = $matches[2];
                }
            } elseif (preg_match('/(?:(.*?\s+?\+)|(\+.*?))(?:\r|$|^)/ix', $group, $matches)) {
                $maxprice = null;
                $minprice = preg_replace('/\s+?\+\s+?/ism', '', $matches[1]);
            }
            $parsedGrouping[] = array(
                'minprice' => $this->raw_number($minprice),
                'maxprice' => $this->raw_number($maxprice),
                'text' => $group
            );
        }
        
        return $parsedGrouping;
        
    }
    
    /**
     * Strip pagingation from an URL
     * @param string $url URL you want to strip
     * @return string Stripped URL
     */
    function strip_page_from_url($url) {
        return preg_replace('/(.+?\/)page-[0-9+]/ism', '$1', $url);
    }
    
    /**
     * Get a full path to an URL
     * @param string $url URL you want to make sure is a full URL
     * @param boolean $use_wp_request Whether to ignore the $_SERVER request and use $wp->request instead
     * @return string Full URL string
     */
    function full_url($url, $use_wp_request = false) {
        if (!$use_wp_request) {
            if (empty($url)) {
                $url = add_query_arg(array());
            }
            $url     = preg_replace('/(.*?)(\/?idx\/.+)/ism', '$2', $url);
            $fullurl = site_url($url);
        } else {
            global $wp;
            $fullurl = trailingslashit($wp->request);
            $fullurl .= ltrim($url, '/');
        }
        
        return $fullurl;
    }
    
    /**
     * Callback function for IDX_Plus::replace_vars()
     *
     * @see $this->replace_vars()
     * @see IDX_Plus:idx()
     * @see IDX_Plus::format_value()
     * @global $idx_plus_vars Passes the "format variables" setting to this callback.
     * @param array $matches Regex matches for `%%tag%%` template tags
     * @return string Value to replace `%%tag%%`
     * @version 2.0.53
     */
    public function _replace_vars_callback($matches) {
        global $idx_plus_vars, $wp;
        
        $value = Bon_IDX::idx(@$matches[1], $idx_plus_vars);
        
        if (!empty($idx_plus_vars['format']) && !empty($value)) {
            $value = Bon_IDX::format_value($value);
        }
        
        return $value;
    }
    
    /**
     * Replace template tags such as %%city%% with the data from the current page.
     * @uses $this->_replace_vars_callback()
     * @param string $text The original content with template tags in it.
     * @param array $idx_plus Optionally pass custom data to be replaced
     * @param boolean $format Format the content (eg: 1,000 instead of 1000)
     * @return string Modified content
     * @version 2.0.54
     */
    function replace_vars($text = '', $idx_plus = array(), $format = false) {
        global $wp, $idx_plus_vars;
        
        if (empty($idx_plus) && isset($wp->wp_idx_content)) {
            $idx_plus = $wp->wp_idx_content;
        }
        
        if (empty($idx_plus)) {
            return $text;
        }
        
        $idx_plus_vars           = $idx_plus;
        $idx_plus_vars['format'] = $format;
        
        $text = preg_replace_callback('/\%\%(.*?)\%\%/ism', array(
            'Bon_IDX_Helper',
            '_replace_vars_callback'
        ), $text);
        
        // Don't keep the global variable around.
        unset($idx_plus_vars);
        
        $text = do_shortcode($text);
        
        return $text;
    }
}

$GLOBALS['bonidxhelper'] = new Bon_IDX_Helper;