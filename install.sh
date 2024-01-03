#!/bin/bash

checkVenv=$( apt-cache policy python3-venv | grep Installed | cut -d " " -f 4 )
if [ $checkVenv = "(none)" ]; then echo sudo apt install python3-venv; exit 1; else echo python3-venv installed.. ${checkVenv} ; fi
checkPip=$( apt-cache policy python3-pip | grep Installed | cut -d " " -f 4 )
if [ $checkPip = "(none)" ]; then echo sudo apt install python3-venv; exit 1; else echo python3-pip installed.. ${checkPip} ; fi

if [ -d env ]; then echo Environment exists..; else echo Setting up virtual environment...; python3 -m venv env; fi

source env/bin/activate

checkLxml=$( pip show lxml )
if [ $? = '1' ]; then echo Installing lxml..; pip install lxml; else echo lxml installed..; fi
checkBs4=$( pip show BeautifulSoup4 )
if [ $? = '1' ]; then echo installing BeautifulSoup4..; pip install BeautifulSoup4 ; else echo BeautifulSoup4 installed..; fi

echo Done.
