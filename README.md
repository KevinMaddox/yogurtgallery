# YogurtGallery
YogurtGallery is a responsive JavaScript web image gallery. It provides an easy setup for creating both fixed and fluid galleries. Supported filetypes are JPG/JPEG, PNG, GIF, BMP, and WEBP.
## Installation
### Overview
YogurtGallery has three main components you'll need to address during installation:
1. A flat-file database for the image catalogue.
2. A folder with image thumbnails for use in the gallery items.
3. The actual JavaScript code and CSS style sheet.
<p>The database is nothing more than a JSON array with relative paths pointing to all your images. A database may look like the following:</p>

```
[
    "winter-photos\/mountains.jpg",
    "winter-photos\/forest.jpg",
    "winter-photos\/lodge.jpg",
    "..\/..\/pixel-art\/poring.png",
    "..\/..\/pixe-art\/drops.png"
]
```
As you can see, you may have paths both above and below the location of the database file. Two PHP scripts are included in this repository: one to generate the database, and another to generate the thumbnails from that database.
### Step 1: Generate the Database
Use the PHP script `util/generate-db.php` to generate the database. Before running it, you'll want to alter the file `config-generate-db.php` to match your needs. For more information, please see section `Specification / Database Generator` further below.<br>When ready, simply run the following command:
```
php generate-db.php
```
### Step 2: Generate the Thumbnails
Use the PHP script `util/generate-thumbnails.php` to generate the thumbnails. Before running it, you'll want to alter the file `config-generate-thumbnails.php` to match your needs. For more information, please see section `Specification / Thumbnail Generator` further below.<br>When ready, simply run the following command:
```
php generate-thumbnails.php
```
### Step 3: Add to Your Webpage
In your head, include the CSS:
```
<link rel="stylesheet" href="../yogurtgallery/src/yogurtgallery.css">
```
Wherever you want on your page (can also be in the head), include the script:
```
<script type="text/javascript" src="../yogurtgallery/src/yogurtgallery.js"></script>
```
In the location where you want the gallery placed on the page, add:
```
<div id="yogurtgallery"></div>
```
At the bottom of your page, after all your content loads, create a new script tag and create a new YogurtGallery instance. There are a variety of parameters which need to be passed and which can be used to configure the behavior and layout of the gallery. For more information, please see section `Specification / YogurtGallery` further below. A sample instance declaration may look like the following:
```
let gallery = new YogurtGallery(
    'res/artwork/illustration/',
    'db.json',
    'thumb',
    {
        'itemsPerPage': 24,
        'layoutType': 'fixed',
        'itemSizeRatio': '1:1.35',
        'itemGap': 0.7,
        'itemsPerRow': 6
    }
);
```
## Specification / YogurtGallery
### Class Declaration
```
YogurtGallery(
    rootPath,
    databaseFileName,
    thumbnailDirectoryName,
    options
)
```
### Arguments
| Arugment | Type | Def. Value | Description |
| --- | --- | --- | --- |
| rootPath | string | N/A | The relative path to the directory where the database file is located. |
| databaseFilename | string | N/A | The file name of the database file. |
| thumbnailDirectoryName | string | N/A | The directory where all the image thumbnails are housed. |
| options | object | See: Configuration Options | Configuration options for initializing instances. |
### Configuration Options
| Option | Type | Def. Value | Description |
| --- | --- | --- | --- |
| layoutType | string | 'fluid' | Can be either `fluid" or `fixed`. Fluid layouts will rearrange gallery items based on the size of the container, whereas fixed layouts will maintain a static number of items per row and will grow/shrink with the size of the container. |
| itemsPerPage | integer | 30 | The maximum number of items per page. |
| maxPaginationLinks | integer | 7 | How many page links should be displayed at one time in the page navigation bar. Prevents excessively-long navigation bars in populated galleries. |
| itemSizeRatio | string | '1:1' | The width-to-height ratio of each gallery item. A ratio of `1:1` is a perfect square, `0.5:1` is tall, and `1:0.5` is long. Values over `1` (e.g. `4:3` or `16:9`) are accepted. |
| itemGap | float | 0 | The spacing between items. Scales with item size. Not measured in any specific units, so play around with it. |
| itemsPerRow | integer | 6 | The static number of items placed on each row. Only applies to fixed layouts. |
| magnification | float | 1 | A means by which to increase or decrease the baseline size of images. Only applies to fluid layouts. Based on an arbitrary baseline of 8em. |
| enableKeyboardNavigation | boolean | true | Toggles the ability to traverse the gallery via keyboard inputs (left/right to change image/page, escape to currently-viewed image. |
### Public Member Functions
#### closePopup()
Closes the full-size image popup window. Useful if you need to do this manually.
## Specification / Database Generator
Edit the configuration options via the file `config-generate-db.php`. When ready, run `php generate-db.php`. More detailed information regarding these options can be found in said configuration file.
| Option | Type | Def. Value | Description |
| --- | --- | --- | --- |
| imageDirectoryRootPath | string | N/A | The relative path of where the directories storing your images are located. |
| imageDirectoryPaths | array[string] | N/A | The paths of the image directories relative to imageDirectoryRootPath. |
| databaseOutputFileName | string | 'db.json' | The name of the JSON database file generated by this script. |
| fileFormats | array[string] | ['jpg', 'png', 'gif'] | Which types of files should be searched for / included in the database. |
| sortingMethod | string | 'ALPHANUMERIC' | How the files should be sorted in the database, which will affect their display order in the gallery. |
| reverseSorting | boolean | false | Whether the files should be sorted, based on the method, in reverse. |
## Specification / Thumbnail Generator
Edit the configuration options via the file `config-generate-db.php`. When ready, run `php generate-db.php`. More detailed information regarding these options can be found in said configuration file.
| Option | Type | Def. Value | Description |
| --- | --- | --- | --- |
| databaseFilePath | string | N/A | The relative path of the JSON database generated via `generate-db.php`. |
| thumbnailDirectoryName | string | N/A | The name of the directory which will be created to house the generated thumbnails. |
| thumbnailSize | integer | 154 | The max width or height of the thumbnails in pixels. |
| jpegQualityLevel | integer | 75 | The quality level of JPEG thumbnails. |
| webpQualityLevel | integer | 80 | The quality level of WEBP thumbnails. |
| pngCompressionLevel | integer | 6 | The quality level of PNG thumbnails. |
## Notes
* Because the paths in the database file are all relative, any changes in directory structure of file placement will require you to modify the file or regenerate the file via the `generate-db.php` script.