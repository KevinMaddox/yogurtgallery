<?php

// - Initialization. -----------------------------------------------------------
echo PHP_EOL;
echo 'Yogurt Gallery - Image Database Generator' . PHP_EOL;
echo 'https://github.com/WallyChantek/yogurtgallery' . PHP_EOL . PHP_EOL;

// - Validate configuration options. -------------------------------------------
$cfg = require_once('config.php');

if (!$cfg)
    exit('Could not locate configuration file.');
else if (!is_array($cfg)) {
    exit(
        'The configuration file was not a valid array. Something\'s broken. '
      . PHP_EOL . 'Please download a fresh copy from the repository if needed.'
    );
}

$validOptions = [
    'imageDirectoryRootPath' => ['string' , ''                           ],
    'imageDirectoryPaths'    => ['array'  , []                           ],
    'databaseOutputFilename' => ['string' , 'db.json'                    ],
    'filetypes'              => ['array'  , ['jpg', 'jpeg', 'png', 'gif']],
    'sortingMethod'          => ['string' , 'ALPHANUMERIC'               ],
    'reverseSorting'         => ['boolean', false                        ]
];

// Set default values for unspecified options.
foreach ($validOptions as $key => $val) {
    if (!array_key_exists($key, $cfg)) {
        $cfg[$key] = $validOptions[$key][1];
        echo "Missing parameter {$key}. ";
        if (!is_array($validOptions[$key][1]))
            echo "Defaulting value to {$validOptions[$key][1]}.";
        else
            echo "Defaulting value to [].";
        echo PHP_EOL . PHP_EOL;
    }
}

// Perform validation.
foreach ($cfg as $key => $val) {
    // Ensure key is allowed.
    if (!array_key_exists($key, $validOptions))
        exit("Invalid configuration option: {$key}. Fix your config file.");
    
    // Ensure value is of a valid type.
    if (gettype($cfg[$key]) !== $validOptions[$key][0]) {
        exit(
            "Invalid value for option {$key}. " . PHP_EOL
          . "Value must be of type {$validOptions[$key][0]} "
          . 'but was of type ' . gettype($cfg[$key]) . '.'
        );
    }
    
    // Validate specific options.
    switch ($key) {
        case 'imageDirectoryRootPath':
            if (strlen($cfg[$key]) === 0) {
                exit(
                    'imageDirectoryRootPath cannot be an empty string. '
                  . 'Did you forget to specify this option in the config?'
                );
            }
            
            $cfg[$key] = sanitizePath($cfg[$key]);
            
            break;
        case 'imageDirectoryPaths' :
            if (count($cfg[$key]) === 0) {
                exit(
                    'imageDirectoryPaths cannot be an empty array. '
                  . 'Did you forget to specify this option in the config?'
                );
            }
            
            for ($i = 0; $i < count($cfg[$key]); $i++) {
                // Ensure path isn't an empty string.
                if (strlen($cfg[$key][$i]) === 0) {
                    exit(
                        'imageDirectoryPaths contains an empty string entry. '
                      . 'This is not allowed.'
                    );
                }
                
                // Check if directory exists.
                if (!is_dir($cfg['imageDirectoryRootPath'] . $cfg[$key][$i])) {
                    exit(
                        'Image directory '
                      . "{$cfg['imageDirectoryRootPath']}{$cfg[$key][$i]} "
                      . "does not exist."
                    );
                }
                
                $cfg[$key][$i] = sanitizePath($cfg[$key][$i]);
            }
            
            break;
        case 'databaseOutputFilename':
            // Ensure path ends with ".json".
            if (substr($cfg[$key], -5) !== '.json')
                $cfg[$key] .= '.json';
            
            break;
        case 'filetypes':
            for ($i = 0; $i < count($cfg[$key]); $i++) {
                if (gettype($cfg[$key][$i]) !== 'string') {
                    exit(
                        'Please ensure all values contained in option'
                      . 'filetypes are strings.'
                    );
                }
            }
            
            break;
        case 'sortingMethod':
            if (!in_array($cfg[$key], array(
                    'ALPHANUMERIC', 'DATE_MODIFIED', 'DATE_CREATED', 'NONE'
                ), true)) {
                exit("{$cfg[$key]} is not a valid value for sortingMethod.");
            }
                
            break;
    }
}



// - Retrieve and sort files to be catalogued in database. ---------------------
// Get list of target folders.
$root = $cfg['imageDirectoryRootPath'];
$folders = $cfg['imageDirectoryPaths'];

// Get complete list of files within folders.
// We'll need to store the parent folder and the file name as split-up arrays.
// This is so we can do a global sort, but still maintain the original folder.
$filePaths = [];
foreach($folders as $folder)
{
    $files = glob($root . $folder . '*');
    foreach ($files as $f)
    {
        if (!in_array(pathinfo($f, PATHINFO_EXTENSION), $cfg['filetypes']))
            continue;
        
        
        array_push($filePaths, [
            dirname(str_replace($root, '', $f)) . '/',
            basename($f)
        ]);
    }
}

// Sort files.
switch ($cfg['sortingMethod']) {
    case 'ALPHANUMERIC':
        usort($filePaths, function($a, $b) {
            return ($a[1] < $b[1]) ? -1 : 1;
        });
        
        break;
    case 'DATE_MODIFIED':
        usort($filePaths, function($a, $b) use ($root) {
            $t1 = filemtime($root . $a[0] . $a[1]);
            $t2 = filemtime($root . $b[0] . $b[1]);
            
            return ($t1 < $t2);
        });
        
        break;
    case 'DATE_CREATED':
        usort($filePaths, function($a, $b) use ($root) {
            $t1 = filectime($root . $a[0] . $a[1]);
            $t2 = filectime($root . $b[0] . $b[1]);
            
            return ($t1 < $t2);
        });
        
        break;
}

// Rever order of sorted files if specified.
if ($cfg['reverseSorting']) {
    usort($filePaths, function($a, $b) {
        return $a <= $b;
    });
}

// Store as single folder/file strings.
$fileNames = [];
for ($i = 0; $i < count($filePaths); $i++)
    array_push($fileNames, $filePaths[$i][0] . $filePaths[$i][1]);

// Save image path list as JSON file.
file_put_contents(
    $root . $cfg['databaseOutputFilename'], 
    json_encode($fileNames, JSON_PRETTY_PRINT)
);


function sanitizePath($path) {
    // Converted slashes and force trailing slash.
    $path = str_replace('\\', '/', $path);
    $path = (substr($path, -1) !== '/' ? $path . '/' : $path);
    
    return $path;
}

?>