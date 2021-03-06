<?php

return [
    'sentiment' => [
        'url'  => '/url/URLGetTextSentiment',
        'text' => '/text/TextGetTextSentiment',
        'html' => '/html/HTMLGetTextSentiment',
    ],
    'sentiment_targeted' => [
        'url'  => '/url/URLGetTargetedSentiment',
        'text' => '/text/TextGetTargetedSentiment',
        'html' => '/html/HTMLGetTargetedSentiment',
    ],
    'author' => [
        'url'  => '/url/URLGetAuthor',
        'html' => '/html/HTMLGetAuthor',
    ],
    'keywords' => [
        'url'  => '/url/URLGetRankedKeywords',
        'text' => '/text/TextGetRankedKeywords',
        'html' => '/html/HTMLGetRankedKeywords',
    ],
    'concepts' => [
        'url'  => '/url/URLGetRankedConcepts',
        'text' => '/text/TextGetRankedConcepts',
        'html' => '/html/HTMLGetRankedConcepts',
    ],
    'entities' => [
        'url'  => '/url/URLGetRankedNamedEntities',
        'text' => '/text/TextGetRankedNamedEntities',
        'html' => '/html/HTMLGetRankedNamedEntities',
    ],
    'category' => [
        'url'  => '/url/URLGetCategory',
        'text' => '/text/TextGetCategory',
        'html' => '/html/HTMLGetCategory',
    ],
    'relations' => [
        'url'  => '/url/URLGetRelations',
        'text' => '/text/TextGetRelations',
        'html' => '/html/HTMLGetRelations',
    ],
    'language' => [
        'url'  => '/url/URLGetLanguage',
        'text' => '/text/TextGetLanguage',
        'html' => '/html/HTMLGetLanguage',
    ],
    'text' => [
        'url'  => '/url/URLGetText',
        'html' => '/html/HTMLGetText',
    ],
    'text_raw' => [
        'url'  => '/url/URLGetRawText',
        'html' => '/html/HTMLGetRawText',
    ],
    'title' => [
        'url'  => '/url/URLGetTitle',
        'html' => '/html/HTMLGetTitle',
    ],
    'feeds' => [
        'url'  => '/url/URLGetFeedLinks',
        'html' => '/html/HTMLGetFeedLinks',
    ],
    'microformats' => [
        'url'  => '/url/URLGetMicroformatData',
        'html' => '/html/HTMLGetMicroformatData',
    ],
    'combined' => [
        'url'  => '/url/URLGetCombinedData',
        'text' => '/text/TextGetCombinedData',
    ],
    'image' => [
        'url' => '/url/URLGetImage',
    ],
    'image_keywords' => [
        'url'   => '/url/URLGetRankedImageKeywords',
        'image' => '/image/ImageGetRankedImageKeywords',
    ],
    'taxonomy' => [
        'url'  => '/url/URLGetRankedTaxonomy',
        'html' => '/html/HTMLGetRankedTaxonomy',
        'text' => '/text/TextGetRankedTaxonomy',
    ],
];