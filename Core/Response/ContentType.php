<?php

namespace Response;

enum ContentType: string
{
    case TEXT_HTML = 'text/html';
    case TEXT_PLAIN = 'text/plain';
    case TEXT_CSS = 'text/css';
    case TEXT_CSV = 'text/csv';
    case TEXT_XML = 'text/xml';
    case TEXT_JAVASCRIPT = 'text/javascript';
    case TEXT_MARKDOWN = 'text/markdown';
    case APPLICATION_JSON = 'application/json';
    case APPLICATION_XML = 'application/xml';
    case APPLICATION_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    case APPLICATION_JAVASCRIPT = 'application/javascript';
    case APPLICATION_PDF = 'application/pdf';
    case APPLICATION_ZIP = 'application/zip';
    case APPLICATION_GZIP = 'application/gzip';
    case APPLICATION_OCTET_STREAM = 'application/octet-stream';
    case APPLICATION_MSWORD = 'application/msword';
    case APPLICATION_VND_MS_EXCEL = 'application/vnd.ms-excel';
    case APPLICATION_VND_MS_POWERPOINT = 'application/vnd.ms-powerpoint';
    case APPLICATION_VND_OPENXMLFORMATS_OFFICEDOCUMENT_WORDPROCESSINGML_DOCUMENT = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    case APPLICATION_VND_OPENXMLFORMATS_OFFICEDOCUMENT_SPREADSHEETML_SHEET = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    case APPLICATION_VND_OPENXMLFORMATS_OFFICEDOCUMENT_PRESENTATIONML_PRESENTATION = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    case MULTIPART_FORM_DATA = 'multipart/form-data';
    case IMAGE_PNG = 'image/png';
    case IMAGE_JPEG = 'image/jpeg';
    case IMAGE_GIF = 'image/gif';
    case IMAGE_SVG_XML = 'image/svg+xml';
    case IMAGE_WEBP = 'image/webp';
    case IMAGE_BMP = 'image/bmp';
    case IMAGE_ICO = 'image/x-icon';
    case AUDIO_MPEG = 'audio/mpeg';
    case AUDIO_OGG = 'audio/ogg';
    case AUDIO_WAV = 'audio/wav';
    case VIDEO_MP4 = 'video/mp4';
    case VIDEO_WEBM = 'video/webm';
    case VIDEO_OGG = 'video/ogg';
}
