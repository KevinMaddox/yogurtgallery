# YogurtGallery
YogurtGallery is a responsive JavaScript web gallery.
## Overview
YogurtGallery has three main components you'll need to address during installation:
1. A flat-file database for the image catalogue.
2. A folder with image thumbnails for use in the gallery items.
3. The actual JavaScript code and CSS style sheet.
The database is nothing more than a JSON array with relative paths pointing to all your images. A database may look like the following:
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
## Installation
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
At the bottom of your page, after all your content loads, create a new script tag and create a new YogurtGallery instance. There are a variety of parameters which need to be passed and which can be used to configure the behavior and layout of the gallery. For more information, please see section `Specification / YogurtGallery` further below. A sample instance declaration may look like the following:
```
let gallery = new YogurtGallery({
    'rootPath': 'res/artwork/illustration/',
    'databaseFilename': 'db.json',
    'thumbnailDirectoryName': 'thumb',
    'itemsPerPage': 24,
    'layoutType': 'fluid',
    'itemSizeRatio': '1:1.35',
    'itemGap': 0.7,
    'itemsPerRow': 6
});
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
| layoutType | string | fluid | Can be either "fluid" or "fixed". Fluid layouts will rearrange gallery items based on the size of the container, whereas fixed layouts will maintain a static number of items per row and will grow/shrink with the size of the container. |
| itemsPerPage | number | 30 | The maximum number of items per page. |
| maxPaginationLinks | number | 7 | How many page links should be displayed at one time in the page navigation bar. Prevents excessively-long navigation bars in populated galleries. |
| itemSizeRatio | string | 1:1 | The width-to-height ratio of each gallery item. A ratio of `1:1` is a perfect square, `0.5:1` is tall, and `1:0.5` is long. Values over `1` (e.g. `4:3` or `16:9`) are accepted. |
| itemGap | number | 0 | The spacing between items. Scales with item size. Not measured in any specific units, so play around with it. |
| itemsPerRow | number | 6 | The static number of items placed on each row. Only applies to fixed layouts. |
| magnification | number | 1 | A means by which to increase or decrease the baseline size of images. Only applies to fluid layouts. |
### Member Functions
#### closePopup()
Closes the full-size image popup window. Useful if you need to do this manually.
## Specification / Database Generator


## Specification / Thumbnail Generator


## Notes
* Because the paths in the database file are all relative, any changes in directory structure of file placement will require you to modify the file or regenerate the file via the `generate-db.php` script.

## TODO Note to self: handle directory conflicts for thumbnails folder