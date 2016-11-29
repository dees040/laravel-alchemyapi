<?php

namespace dees040\AlchemyApi;

use dees040\AlchemyApi\Exceptions\AlchemyApiFlavorNotFound;

class AlchemyApi
{
    /**
     * The API key.
     *
     * @var string
     */
    private $apiKey;

    /**
     * Array with endpoints.
     *
     * @var array
     */
    private $endpoints;

    /**
     * The base url to send the api requests to.
     *
     * @var string
     */
    private $baseUrl = 'http://access.alchemyapi.com/calls';

    /**
     * Constructor
     *
     * @param string
     * @param boolean
     * @return void
     */
    public function __construct($key = null, $use_https = false)
    {
        $this->apiKey = $key ?: env('ALCHEMYAPI_KEY');

        if ($use_https) {
            $this->baseUrl = str_replace('http', 'https', $this->baseUrl);
        }

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
        $this->throwExceptionOnUnknownFlavor($flavor, 'image_keywords', 'Image tagging');

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
    public function entities($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'entities', 'Entity extraction');

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
    public function keywords($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'keywords', 'Keyword extraction');

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
    public function concepts($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'concepts', 'Concept tagging');

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
    public function sentiment($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'sentiment', 'Sentiment analysis');

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
        $this->throwExceptionOnUnknownFlavor($flavor, 'sentiment_targeted', 'Targeted sentiment');

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
    public function text($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'text', 'Clean text extraction');

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
    public function text_raw($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'text_raw', 'Raw text extraction');

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
    public function author($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'author', 'Author extraction');

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
    public function language($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'language', 'Language detection');

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
    public function title($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'title', 'Title text extraction');

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
    public function relations($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'relations', 'Relation extraction');

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
    public function category($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'category', 'Text categorization');

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
    public function feeds($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'feeds', 'Feed detection');

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
    public function microformats($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'microformats');

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
    public function imageExtraction($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'image', 'Image Extraction parsing');

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
    public function taxonomy($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'taxonomy');

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
    public function combined($data, $options, $flavor = 'text')
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, 'combined');

        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->analyze($this->endpoints['combined'][$flavor], $options);
    }

    /**
     * Indicate if flavor exists for given endpoint.
     *
     * @param string $flavor
     * @param string $endpoint
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
     * Throw an exception if we have an unknown flavor.
     *
     * @param string $flavor
     * @param string $endpoint
     * @param null $errorStart
     * @throws AlchemyApiFlavorNotFound
     */
    private function throwExceptionOnUnknownFlavor($flavor, $endpoint, $errorStart = null)
    {
        if (! $this->hasFlavor($flavor, $endpoint)) {
            throw new AlchemyApiFlavorNotFound(
                sprintf("%s for %s not available", $errorStart ?: $endpoint . ' parsing', $flavor)
            );
        }
    }

    /**
     * HTTP Request wrapper that is called by the endpoint functions. This function is not intended to be called
     * through an external interface. It makes the call, then converts the returned JSON string into a PHP object.
     *
     * @param $endpoint
     * @param $params
     * @param null $imageData
     * @return array|mixed
     */
    private function analyze($endpoint, $params, $imageData = null)
    {
        //Insert the base URL
        $url = $this->baseUrl . $endpoint;
        $content = http_build_query($params);

        if (! is_null($imageData)) {
            $url = $url . '?' . http_build_query($params);
            $content = $imageData;
        }

        //Add the API Key and set the output mode to JSON
        $params['apikey'] = $this->apiKey;
        $params['outputMode'] = 'json';

        //Create the HTTP header
        $header = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencode',
                'content' => $content
            ]
        ];

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
        return $this->analyze($endpoint, $params, $imageData);
    }

    /**
     * Set the key dynamically.
     *
     * @param $key
     * @return AlchemyApi
     */
    public function setKey($key)
    {
        $this->apiKey = $key;

        return $this;
    }
}