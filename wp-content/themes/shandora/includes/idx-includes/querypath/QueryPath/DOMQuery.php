<?php
/**
 * @file
 * This houses the class formerly called QueryPath.
 *
 * As of QueryPath 3.0.0, the class was renamed QueryPath::DOMQuery. This
 * was done for a few reasons:
 * - The library has been refactored, and it made more sense to call the top
 *   level class QueryPath. This is not the top level class.
 * - There have been requests for a JSONQuery class, which would be the 
 *   natural complement of DOMQuery.
 */

namespace QueryPath;

use \QueryPath\CSS\QueryPathEventHandler;
use \QueryPath;


/**
 * The DOMQuery object is the primary tool in this library.
 *
 * To create a new DOMQuery, use QueryPath::with() or qp() function.
 *
 * If you are new to these documents, start at the QueryPath.php page.
 * There you will find a quick guide to the tools contained in this project.
 *
 * A note on serialization: DOMQuery uses DOM classes internally, and those
 * do not serialize well at all. In addition, DOMQuery may contain many
 * extensions, and there is no guarantee that extensions can serialize. The
 * moral of the story: Don't serialize DOMQuery.
 *
 * @see qp()
 * @see QueryPath.php
 * @ingroup querypath_core
 */
class DOMQuery implements \QueryPath\Query, \IteratorAggregate, \Countable {

  /**
   * Default parser flags.
   *
   * These are flags that will be used if no global or local flags override them.
   * @since 2.0
   */
  const DEFAULT_PARSER_FLAGS = NULL;

  const JS_CSS_ESCAPE_CDATA = '\\1';
  const JS_CSS_ESCAPE_CDATA_CCOMMENT = '/* \\1 */';
  const JS_CSS_ESCAPE_CDATA_DOUBLESLASH = '// \\1';
  const JS_CSS_ESCAPE_NONE = '';

  //const IGNORE_ERRORS = 1544; //E_NOTICE | E_USER_WARNING | E_USER_NOTICE;
  private $errTypes = 771; //E_ERROR; | E_USER_ERROR;

  /**
   * The base DOMDocument.
   */
  protected $document = NULL;
  private $options = array(
    'parser_flags' => NULL,
    'omit_xml_declaration' => FALSE,
    'replace_entities' => FALSE,
    'exception_level' => 771, // E_ERROR | E_USER_ERROR | E_USER_WARNING | E_WARNING
    'ignore_parser_warnings' => FALSE,
    'escape_xhtml_js_css_sections' => self::JS_CSS_ESCAPE_CDATA_CCOMMENT,
  );
  /**
   * The array of matches.
   */
  protected $matches = array();
  /**
   * The last array of matches.
   */
  protected $last = array(); // Last set of matches.
  private $ext = array(); // Extensions array.

  /**
   * The number of current matches.
   *
   * @see count()
   */
  public $length = 0;

  /**
   * Constructor.
   *
   * Typically, a new DOMQuery is created by QueryPath::with(), QueryPath::withHTML(),
   * qp(), or htmlqp().
   *
   * @param mixed $document
   *   A document-like object.
   * @param string $string
   *   A CSS 3 Selector
   * @param array $options
   *   An associative array of options.
   * @see qp()
   */
  public function __construct($document = NULL, $string = NULL, $options = array()) {
    $string = trim($string);
    $this->options = $options + Options::get() + $this->options;

    $parser_flags = isset($options['parser_flags']) ? $options['parser_flags'] : self::DEFAULT_PARSER_FLAGS;
    if (!empty($this->options['ignore_parser_warnings'])) {
      // Don't convert parser warnings into exceptions.
      $this->errTypes = 257; //E_ERROR | E_USER_ERROR;
    }
    elseif (isset($this->options['exception_level'])) {
      // Set the error level at which exceptions will be thrown. By default,
      // QueryPath will throw exceptions for
      // E_ERROR | E_USER_ERROR | E_WARNING | E_USER_WARNING.
      $this->errTypes = $this->options['exception_level'];
    }

    // Empty: Just create an empty QP.
    if (empty($document)) {
      $this->document = isset($this->options['encoding']) ? new \DOMDocument('1.0', $this->options['encoding']) : new \DOMDocument();
      $this->setMatches(new \SplObjectStorage());
    }
    // Figure out if document is DOM, HTML/XML, or a filename
    elseif (is_object($document)) {

      // This is the most frequent object type.
      if ($document instanceof \SplObjectStorage) {
        $this->matches = $document;
        if ($document->count() != 0) {
          $first = $this->getFirstMatch();
          if (!empty($first->ownerDocument)) {
            $this->document = $first->ownerDocument;
          }
        }
      }
      elseif ($document instanceof DOMQuery) {
        //$this->matches = $document->get(NULL, TRUE);
        $this->setMatches($document->get(NULL, TRUE));
        if ($this->matches->count() > 0)
          $this->document = $this->getFirstMatch()->ownerDocument;
      }
      elseif ($document instanceof \DOMDocument) {
        $this->document = $document;
        //$this->matches = $this->matches($document->documentElement);
        $this->setMatches($document->documentElement);
      }
      elseif ($document instanceof \DOMNode) {
        $this->document = $document->ownerDocument;
        //$this->matches = array($document);
        $this->setMatches($document);
      }
      elseif ($document instanceof \SimpleXMLElement) {
        $import = dom_import_simplexml($document);
        $this->document = $import->ownerDocument;
        //$this->matches = array($import);
        $this->setMatches($import);
      }
      else {
        throw new \QueryPath\Exception('Unsupported class type: ' . get_class($document));
      }
    }
    elseif (is_array($document)) {
      //trigger_error('Detected deprecated array support', E_USER_NOTICE);
      if (!empty($document) && $document[0] instanceof \DOMNode) {
        $found = new \SplObjectStorage();
        foreach ($document as $item) $found->attach($item);
        //$this->matches = $found;
        $this->setMatches($found);
        $this->document = $this->getFirstMatch()->ownerDocument;
      }
    }
    elseif ($this->isXMLish($document)) {
      // $document is a string with XML
      $this->document = $this->parseXMLStrin