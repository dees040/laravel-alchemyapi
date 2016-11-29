<?php

namespace dees040\AlchemyAPI;

class AlchemyAPI
{
    /**
     * The API key.
     *
     * @var string
     */
    private $_api_key;

    /**
     * Array with endpoints.
     *
     * @var array
     */
    private $endpoints;

    /**
     * @var string
     */
    private $_base_url;

    /**
     * @var string
     */
    private $_BASE_HTTP_URL = 'http://access.alchemyapi.com/calls';

    /**
     * @var string
     */
    private $_BASE_HTTPS_URL = 'https://access.alchemyapi.com/calls';

    /**
     * Constructor
     *
     * @param string
     * @param boolean
     * @return void
     */
    public function __construct($key = null, $use_https = false)
    {
        $this->_api_key = $key ?: env('ALCHEMYAPI_KEY');

        $this->_base_url = $use_https ? $this->_BASE_HTTPS_URL : $this->_BASE_HTTP_URL;

        //Initialize the API Endpoints
        $this->endpoints = require(__DIR__ . DIRECTORY_SEPARATOR . 'endpoints.php');
    }

    /**
     * Returns tag for an image URL or included image in the body of the request.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/image-tagging/
     *
     * @param $flavor
     * @param $image
     * @param $options
     * @return array|mixed
     */
    public function image_keywords($flavor, $image, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'image_keywords')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Image tagging for ' . $flavor . ' not available'];
        }

        //Add the image to the options and analyze
        if ($flavor == 'url') {
            $options[$flavor] = $image;

            return $this->analyze($this->endpoints['image_keywords'][$flavor], $options);
        } else {
            return $this->analyzeImage($this->endpoints['image_keywords'][$flavor], $options, $image);
        }
    }

    /**
     * Get the entities from a text.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/entity-extraction/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function entities($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'entities')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Entity extraction for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['entities'][$flavor], $options);
    }

    /**
     * Extracts the keywords from text, a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/keyword-extraction/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function keywords($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'keywords')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Keyword extraction for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['keywords'][$flavor], $options);
    }

    /**
     * Tags the concepts for text, a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/concept-tagging/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function concepts($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'concepts')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Concept tagging for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['concepts'][$flavor], $options);
    }

    /**
     * Calculates the sentiment for text, a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/sentiment-analysis/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function sentiment($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'sentiment')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Sentiment analysis for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['sentiment'][$flavor], $options);
    }

    /**
     * Calculates the targeted sentiment for text, a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/sentiment-analysis/
     *
     * @param $flavor
     * @param $data
     * @param $target
     * @param $options
     * @return array|mixed
     */
    public function sentiment_targeted($flavor, $data, $target, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'sentiment_targeted')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Targeted sentiment analysis for ' . $flavor . ' not available'];
        }

        if (! $target) {
            return ['status' => 'ERROR', 'statusInfo' => 'targeted sentiment requires a non-null target'];
        }

        //Add the URL encoded data to the options and analyze
        $options[$flavor] = $data;
        $options['target'] = $target;

        return $this->analyze($this->endpoints['sentiment_targeted'][$flavor], $options);
    }

    /**
     * Extracts the cleaned text (removes ads, navigation, etc.) for text, a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/text-extraction/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function text($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'text')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Clean text extraction for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['text'][$flavor], $options);
    }

    /**
     * Extracts the raw text (includes ads, navigation, etc.) for a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/text-extraction/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function text_raw($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'text_raw')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Raw text extraction for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['text_raw'][$flavor], $options);
    }

    /**
     * Extracts the author from a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/author-extraction/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function author($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'author')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Author extration for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['author'][$flavor], $options);
    }


    /**
     * Detects the language for text, a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/api/language-detection/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function language($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'language')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Language detection for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['language'][$flavor], $options);
    }

    /**
     * Extracts the title for a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/text-extraction/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function title($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'title')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Title text extraction for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['title'][$flavor], $options);
    }

    /**
     * Extracts the relations for text, a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/relation-extraction/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function relations($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'relations')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Relation extraction for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['relations'][$flavor], $options);
    }

    /**
     * Categorizes the text for text, a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/text-categorization/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function category($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'category')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Text categorization for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['category'][$flavor], $options);
    }

    /**
     * Detects the RSS/ATOM feeds for a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/feed-detection/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function feeds($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'feeds')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Feed detection for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['feeds'][$flavor], $options);
    }

    /**
     * Parses the microformats for a URL or HTML.
     *
     * For an overview, please refer to: http://www.alchemyapi.com/products/features/microformats-parsing/
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function microformats($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'microformats')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Microformat parsing for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['microformats'][$flavor], $options);
    }

    /**
     * Extracts main image from a URL
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function imageExtraction($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'image')) {
            return ['status' => 'ERROR', 'statusInfo' => 'Image Extraction parsing for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['image'][$flavor], $options);
    }

    /**
     * Taxonomy classification operations.
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function taxonomy($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'taxonomy')) {
            return ['status' => 'ERROR', 'statusInfo' => 'taxonomy parsing for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['taxonomy'][$flavor], $options);
    }

    /**
     * Get a combined set of results.
     *
     * @param $flavor
     * @param $data
     * @param $options
     * @return array|mixed
     */
    public function combined($flavor, $data, $options)
    {
        //Make sure this request supports the flavor
        if (! $this->hasFlavor($flavor, 'combined')) {
            return ['status' => 'ERROR', 'statusInfo' => 'combined parsing for ' . $flavor . ' not available'];
        }

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['combined'][$flavor], $options);
    }

    /**
     * Indicate if flavor exists for given endpoint.
     *
     * @param $flavor
     * @param $endpoint
     * @return bool
     */
    private function hasFlavor($flavor, $endpoint)
    {
        if (! array_key_exists($flavor, $this->endpoints[$endpoint])) {
            return false;
        }

        return true;
    }

    /**
     * HTTP Request wrapper that is called by the endpoint functions. This function is not intended to be called
     * through an external interface. It makes the call, then converts the returned JSON string into a PHP object.
     *
     * @param $endpoint
     * @param $params
     * @return array|mixed
     */
    private function analyze($endpoint, $params)
    {
        //Insert the base URL
        $url = $this->_base_url . $endpoint;

        //Add the API Key and set the output mode to JSON
        $params['apikey'] = $this->_api_key;
        $params['outputMode'] = 'json';

        //Create the HTTP header
        $header = ['http' => ['method' => 'POST', 'header' => 'Content-Type: application/x-www-form-urlencode', 'content' => http_build_query($params)]];

        //Fire off the HTTP Request
        try {
            $fp = @fopen($url, 'rb', false, stream_context_create($header));
            $response = @stream_get_contents($fp);
            fclose($fp);

            return json_decode($response, true);
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'statusInfo' => 'Network error'];
        }
    }

    /**
     * Use to create request for image API
     *
     * @param $endpoint
     * @param $params
     * @param $imageData
     * @return array|mixed
     */
    private function analyzeImage($endpoint, $params, $imageData)
    {
        //Add the API Key and set the output mode to JSON
        $params['apikey'] = $this->_api_key;
        $params['outputMode'] = 'json';

        //Insert the base URL
        $url = $this->_base_url . $endpoint . '?' . http_build_query($params);

        //Create the HTTP header
        $header = ['http' => ['method' => 'POST', 'header' => 'Content-Type: application/x-www-form-urlencode', 'content' => $imageData]];

        //Fire off the HTTP Request
        try {
            $fp = @fopen($url, 'rb', false, stream_context_create($header));
            $response = @stream_get_contents($fp);
            fclose($fp);

            return json_decode($response, true);
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'statusInfo' => 'Network error'];
        }
    }
}