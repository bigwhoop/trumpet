# Trumpet

The presentation tool for PHP developers.

[![Build Status](https://travis-ci.org/bigwhoop/trumpet.svg?branch=master)](https://travis-ci.org/bigwhoop/trumpet)
[![Code Coverage](https://scrutinizer-ci.com/g/bigwhoop/trumpet/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bigwhoop/trumpet/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bigwhoop/trumpet/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bigwhoop/trumpet/?branch=master)


## Quick Guide

    composer global require bigwhoop/trumpet
    mkdir ~/Presentations && cd ~/Presentations
    trumpet
    
Go to [http://localhost:8075/](http://localhost:8075/).


## Features

- Runs in the browser
- Uses yaml for metadata
- Uses markdown (extra) for slides
- Supports interactive elements like:
    - Running example code
    - Embedding of text files (HTML, CSS, etc.)
    - Dynamic embedding of PHP code (whole files, classes, methods, functions, lines)
    - Embedding images with automatic resizing, cropping, etc.
    - Summary Quotes from Wikipedia
    - ...
- Themeable (using Twig)

## Installation

    composer global require bigwhoop/trumpet

Make sure that the `~/.composer/vendor/bin` folder is in your `PATH` environment variable.
This [blog post](http://akrabat.com/global-installation-of-php-tools-with-composer/) should help you.

## Usage

By default trumpet uses PHP's built-in webserver. Just run

    trumpet

to start it. You should see something like this:

    λ trumpet
    2015-05-02 22:25:48 [INFO] Starting webserver on localhost:8075 ...

So fire up your browser and head over to [http://localhost:8075/](http://localhost:8075/).

**Trumpet always uses the directory from where it is started to locate presentations.**

### Workspaces

It's recommended to create a new folder for all your presentations.

    mkdir ~/Presentations
    cd ~/Presentations

#### Themes

You can download or create custom themes. Trumpet will look for a `.theme` directory in the current working directory.


## Presentations

Trumpet presentations are stored in files with a `.trumpet` extension. Here's an example:

    title: Our test presentation
    subtitle: An optional sub-title
    date: 2015-05-20
    authors:
    - Max Microwave, Some Company Lts., @themax
    - name: Freddy Frypan
      email: freddy@example.org
      twitter: freddy
      company: Another Company
      website: www.example.org
      skype: freedy.frypan
    
    slides: |
      # This is a title, it's on its own page
      
      ## This is a subtitle, it will create a new slide
      
      This is some example text.
      
      ### And this is a sub-subtitle
      
      And some more text. Yay.
      
      ## A new slide?
      
      Yep, like I told you. Titles and subtitles always create a new slide.
      
      - These are
      - bullet points.


### Slides

- Slides are written in Markdown (Extra).
- Headings 1 will be shown on their own slide.
- Headings 2 will force a new slide.

## Commands

In your `slides` markup you can use Commands to make the presentation dynamic.

### Code Command

Include PHP code into you slides. Maybe in the future other programming languages will be supported.

Let's say we have the following `Number.php` located in `~/presentations/Number.php`:

    <?php
    namespace My\Library;
    
    class Number
    {
        private $value = 0;
        
        public function __construct($value) { $this->value = $value; }
        
        public function getValue() { return $this->value; }
        
        public function add(Number $n)
        {
            return new Number($this->value + $n->getValue());
        }
    }
    
    $n1 = new Number(5);
    $n2 = $n1->add(new Number(3));
    echo "5 + 3 = {$n2->getValue()}";
    
    function add($a, $b) {
        return $a + $b;
    }

#### File

Command:

    !code Number.php

Output:

    <?php
    namespace My\Library;
    
    class Number
    {
        private $value = 0;
        
        public function __construct($value) { $this->value = $value; }
        
        public function getValue() { return $this->value; }
        
        public function add(Number $n)
        {
            return new Number($this->value + $n->getValue());
        }
    }
    
    $n1 = new Number(5);
    $n2 = $n1->add(new Number(3));
    echo "5 + 3 = {$n2->getValue()}";
    
    function add($a, $b) {
        return $a + $b;
    }

#### Class

Command:

    !code Number.php class My\Library\Number

Output:

    class Number
    {
        private $value = 0;
        public function __construct($value)
        {
            $this->value = $value;
        }
        public function getValue()
        {
            return $this->value;
        }
        public function add(Number $n)
        {
            return new Number($this->value + $n->getValue());
        }
    }

#### Method

Command:

    !code Number.php method My\Library\Number add

Output:

    public function add(\My\Library\Number $n)
    {
        return new Number($this->value + $n->getValue());
    }

#### Function

Command:

    !code Number.php function My\Library\add

Output:

    function add($a, $b)
    {
        return $a + $b;
    }

#### Line

Command:

    !code Number.php line 2
    !code Number.php line 18-20

Output:

    namespace My\Library;
    $n1 = new Number(5);
    $n2 = $n1->add(new Number(3));
    echo "5 + 3 = {$n2->getValue()}";

#### File Abstract

Command:

    !code Number.php abstract

Output:

    CLASSES (1)
     My\Library\Number
      __construct()
      getValue()
      add()
    
    FUNCTIONS (1)
     My\Library\add


## Exec Command

Executes a PHP file.

Command:

    # See "Code Command" for the contents of the Number.php file
    !exec Number.php

Output:

    5 + 3 = 8


## Image Command

Include images in your slides and optionally resizes them.

    !image image.jpg 500x400                # Resizes image while keeping its ratio
    !image image.jpg 500x0                  # Resizes image while keeping its ratio so that the width is 500px 
    !image image.jpg 0x400                  # Resizes image while keeping its ratio so that the height is 400px
    !image image.jpg 0x400                  # Resizes image while keeping its ratio so that the height is 400px
    !image image.jpg 500x400 stretch
    !image image.jpg 500x400 fit
    !image image.jpg 500x400 crop


## Include Command

Copy contents of files one-to-one into your slides. This allows you to move example code (CSS, JS) or single slides
into separate files.

Let's assume we have a `slides.md` file like this:

    ## 2nd Slide
    This file was included into my presentation.

And in our `.trumpet` file we'd have:

    slides: |
      # Hello
      
      !include slides.md

Th result would be equivalent to this:

    slides: |
      # Hello
      
      ## 2nd Slide
      This file was included into my presentation.

### Line mode

There you can also include only a certain range of lines.

    !include slides.md line 5           # Only the 5th line
    !include slides.md line 9-12        # Lines 9 - 12

## Wikipedia Summary

Shows the summary for a given topic in a blockquote.

Command:

    !wiki TOPIC [NUM_SENTEMCES]

Example:

    !wiki "Theme (computing)"

Output:

    > In computing, a theme is a preset package containing graphical appearance details. A theme
    > usually comprises a set of shapes and colors for the graphical control elements, the window
    > decoration and the window. Themes are used to customize the look and feel of a piece of
    > computer software or of an operating system.
