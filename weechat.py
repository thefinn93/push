# -*- coding: utf-8 -*-
#
# Copyright (C) 2006-2012 Sebastien Helleu <flashcode@flashtux.org>
# Copyright (C) 2011 Nils GÃ¶rs <weechatter@arcor.de>
# Copyright (C) 2011 ArZa <arza@arza.us>
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#
# Run command on highlight/private message or new DCC.
#
# History:
#
# 2013-05-21, Finn Herzfeld <thefinn93@thefinn93.com>
#     version 0.9: forked beep.py, modified it to ping the users phone when a highlight came in
# 2012-01-03, Sebastien Helleu <flashcode@flashtux.org>:
#     version 0.8: make script compatible with Python 3.x
# 2011-04-16, ArZa <arza@arza.us>:
#     version 0.7: fix default beep command
# 2011-03-11, nils_2 <weechatter@arcor.de>:
#     version 0.6: add additional command options for dcc and highlight
# 2011-03-09, nils_2 <weechatter@arcor.de>:
#     version 0.5: add option for beep command and dcc
# 2009-05-02, Sebastien Helleu <flashcode@flashtux.org>:
#     version 0.4: sync with last API changes
# 2008-11-05, Sebastien Helleu <flashcode@flashtux.org>:
#     version 0.3: conversion to WeeChat 0.3.0+
# 2007-08-10, Sebastien Helleu <flashcode@flashtux.org>:
#     version 0.2: upgraded licence to GPL 3
# 2006-09-02, Sebastien Helleu <flashcode@flashtux.org>:
#     version 0.1: initial release
#

SCRIPT_NAME    = 'push'
SCRIPT_AUTHOR  = 'Finn Herzfeld <thefinn93@thefinn93.com>'
SCRIPT_VERSION = '0.9'
SCRIPT_LICENSE = 'GPL3'
SCRIPT_DESC    = 'Push a message to your phone when you get a PM/DCC/highlight'

import_ok = True

try:
    import weechat
except:
    print('This script must be run under WeeChat.')
    print('Get WeeChat now at: http://www.weechat.org/')
    import_ok = False

try:
    import os
    import sys
    import requests
except ImportError as message:
    print('Missing package(s) for %s: %s' % (SCRIPT_NAME, message))
    import_ok = False

default_options = {
    'highlight' : "on",
    'pv'        : "on",
    'dcc'       : "on",
    'token'	: ""
}
options = {}


def init_config():
    global default_options, options
    for option, default_value in default_options.items():
        if not weechat.config_is_set_plugin(option):
            weechat.config_set_plugin(option, default_value)
        options[option] = weechat.config_get_plugin(option)

def config_changed(data, option, value):
    init_config()
    return weechat.WEECHAT_RC_OK

def push_cb(data, signal, signal_data):
    global options
    if data in options and "token" in options:
        if options[data] == "on":
            requests.post("http://push.thefinn93.com/send", {'message': signal_data, 'title': "Weechat!", "token": options['token']})
    return weechat.WEECHAT_RC_OK

def push_test(data, buffer, message):
    global options
    requests.post("http://push.thefinn93.com/send", {'message': message, 'title': "Weechat (test)", "token": options['token']})
    return weechat.WEECHAT_RC_OK

if __name__ == '__main__' and import_ok:
    if weechat.register(SCRIPT_NAME, SCRIPT_AUTHOR, SCRIPT_VERSION,
                        SCRIPT_LICENSE, SCRIPT_DESC, '', ''):
        init_config()
        weechat.hook_config('plugins.var.python.%s.*' % SCRIPT_NAME, 'config_changed', '')
        weechat.hook_signal('weechat_highlight', 'push_cb', 'highlight')
        weechat.hook_signal('irc_pv', 'push_cb', 'pv')
        weechat.hook_signal('irc_dcc', 'push_cb', 'dcc')
        weechat.hook_command("push","Push a message to your phone, mostly to test it", "[message]",
                             "message: The message to send to your phone", "%(message)", "push_test", "")
