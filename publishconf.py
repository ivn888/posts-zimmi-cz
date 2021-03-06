#!/usr/bin/env python
# -*- coding: utf-8 -*- #
from __future__ import unicode_literals

# This file is only used if you use `make publish` or
# explicitly specify it as your config file.

import os
import sys
sys.path.append(os.curdir)
from pelicanconf import *

SITEURL = 'https://www.zimmi.cz/posts'
RELATIVE_URLS = False
THEME = 'content/theme/simple'
FEED_DOMAIN = SITEURL
FEED_ALL_ATOM = 'atom.xml'
CATEGORY_FEED_ATOM = None
FEED_ALL_RSS = 'feed.xml'

DELETE_OUTPUT_DIRECTORY = True
TYPOGRIFY = True

# Following items are often useful when publishing

ARTICLE_URL = '{date:%Y}/{slug}/'
ARTICLE_SAVE_AS = '{date:%Y}/{slug}/index.html'
GOOGLE_ANALYTICS = 'UA-43432739-2'

PLUGIN_PATHS = ['content/plugins']
PLUGINS = ['neighbors', 'assets', 'optimize_images']
