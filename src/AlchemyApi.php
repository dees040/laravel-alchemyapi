<?php

namespace dees040\AlchemyApi;

use dees040\AlchemyApi\Exceptions\AlchemyApiFlavorNotFound;
use dees040\AlchemyApi\Exceptions\SentimentTargetIsNullable;

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
    public function imageKeywords($flavor, $image, $options)
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'entities', $options, 'Entity extraction');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'keywords', $options, 'Keyword extraction');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'concepts', $options, 'Concept tagging');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'sentiment', $options, 'Sentiment analysis');
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
     * @throws SentimentTargetIsNullable
     */
    public function sentimentTargeted($flavor, $data, $target, $options)
    {
        if (! $target) {
            throw new SentimentTargetIsNullable("Targeted sentiment requires a non-null target.");
        }

        //Add the URL encoded data to the options and analyze
        $options[$flavor] = $data;
        $options['target'] = $target;

        return $this->execute($flavor, 'sentiment_targeted', $options, 'Targeted sentiment');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'text', $options, 'Clean text extraction');
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
    public function textRaw($data, $options, $flavor = 'text')
    {
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'text_raw', $options, 'Raw text extraction');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'author', $options, 'Author extraction');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'language', $options, 'Language detection');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'title', $options, 'Title text extraction');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'relations', $options, 'Relation extraction');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'category', $options, 'Text categorization');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'feeds', $options, 'Feed detection');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'microformats', $options);
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'image', $options, 'Image Extraction parsing');
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'taxonomy', $options);
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
        //Add the data to the options and analyze
        $options[$flavor] = $data;

        return $this->execute($flavor, 'combined', $options);
    }

    /**
     * Execute the before analyze function.
     *
     * @param $flavor
     * @param $options
     * @param $endpoint
     * @param null $error
     * @return array|mixed
     */
    private function execute($flavor, $endpoint, $options, $error = null)
    {
        //Make sure this request supports the flavor
        $this->throwExceptionOnUnknownFlavor($flavor, $endpoint, $error);

        return $this->analyze($this->getFullEndPoint($endpoint, $flavor), $options);
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
        list($url, $content) = $this->createAnalyzeData($endpoint, $params, $imageData);

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
     * Create the required api information.
     *
     * @param string $endpoint
     * @param array $params
     * @param mixed $imageData
     * @return array
     */
    private function createAnalyzeData($endpoint, $params, $imageData)
    {
        //Add the API Key and set the output mode to JSON
        $params['apikey'] = $this->apiKey;
        $params['outputMode'] = 'json';

        //Insert the base URL
        $url = $this->baseUrl . $endpoint;
        $content = http_build_query($params);

        if (! is_null($imageData)) {
            $url = $url . '?' . http_build_query($params);
            $content = $imageData;
        }

        return [$url, $content];
    }

    /**
     * Get the a flavor of the given endpoint.
     *
     * @param $endpoint
     * @param $flavor
     * @return mixed
     */
    private function getFullEndPoint($endpoint, $flavor)
    {
        return $this->endpoints[$endpoint][$flavor];
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