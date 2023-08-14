#!/bin/bash
cat bin/splash.f
echo Prerequisites: python3-venv, BeautifulSoup4, lxml
echo Enabling virtual environment..

if [ -d "env" ]; 
    then
        source env/bin/activate
        echo "Virutal environment activated"
    else
        checkVenv=$( apt-cache policy python3-venv | grep Installed | cut -d " " -f 4 )
        if [ $checkVenv = "(none)" ]; then echo "Fatal: python3-venv not installed"; exit 1; else echo python3-venv installed.. ${checkVenv} ; fi
        echo "Setting up virtual environment.."
        python3 -m venv env/
fi

# Checking whether or not pip, lxml, and bs4 are installed
checkPip=$( apt-cache policy python3-pip | grep Installed | cut -d " " -f 4 )
if [ $checkPip = "(none)" ]; then echo "Fatal: python3-pip not installed"; exit 1; else echo python3-pip installed.. ${checkPip}; fi
checkLxml=$( pip show lxml )
if [ $? = '1' ]; then echo Fatal: python3-pip package lxml not installed; exit 1; else echo lxml installed..; fi
checkBs4=$( pip show BeautifulSoup4 )
if [ $? = '1' ]; then echo Fatal: python3-pip package BeaituflSoup4 not intalled; exit 1; else echo BeautifulSoup4 installed..; fi

echo Necessary packages present..
if [ -d bin/pages ];
    then
    echo "bin/pages present.."
    else
    mkdir bin/pages
    echo "mkdir bin/pages"
fi

function Execute(){
    clear
    cat bin/splash.f
    echo Run:
    echo "1) Macbooks Pros"
    echo "2) Macbooks & Macbook Pros"
    read entry
    case $entry in
        1)
            echo Selected: Macbook Pros
            pageNum=1
        ;;
        2)
            echo "Selected: Macbooks & Macbook Pros"
            pageNum=5
        ;;
    esac
    cat bin/splash.f
    echo Retrieving bulk data via wget...
    echo Clearing FilteredOutput.txt...
    echo Clearing bin/save_loc.txt...
    > bin/FilteredOutput.txt
    > bin/save_loc.txt
    echo '0' > bin/save_loc.txt
    pageLim=$(( $pageNum + 3 ))
    fileNum=1
    clear
    while [ $pageNum -le $pageLim ];
        do 
            clear
            cat bin/splash.f
            echo Retrieving bulk data via wget... ${fileNum}/4
            echo
            lineNum=$( head -n $pageNum bin/wget-links.txt | tail -1 )
            #echo "Retrieving $fileNum/4: $lineNum"
            wget -v -O index$fileNum.html $lineNum #change
            pageNum=$(( $pageNum + 1 ))
            fileNum=$(( $fileNum + 1 ))
        done
        echo "Pushing output to bin... Done"
        mv index* bin/pages
        echo "Filtering... Done"
        python3 src/stringProcessor.py
        echo "Outputting links to FilteredOutput.txt... Done"
        echo
        echo "Press any key to continue"
        read input
        clear
}

function Clear(){
    echo -n Clearing bin... 
    rm bin/pages/index* 2>> bin/error_log.txt
    if [ $? == 0 ]
        then
            echo OK.

        else
            echo Failed: Nothing to clear.
    fi
    #echo done.
    echo
}

function Regenerate(){
    if [ -f bin/pages/index1.html ];
        then
        > bin/FilteredOutput.txt
        > bin/save_loc.txt
        echo '0' > bin/save_loc.txt
        python3 src/stringProcessor.py
        echo
        echo "Press any key to continue"
        read input
        else
        echo No bulk data to regenerate from.
    fi
    clear
}

function readOutput(){
    linkTotal=$( wc -l bin/FilteredOutput.txt | cut -d " " -f 1 )
    linkTotal=$(( $linkTotal / 2 ))
    savePosition=$( cat bin/save_loc.txt )
    iteration=1
    linkNum=1
    if [ $savePosition != '0' ]
        then
        decodedPosition=$(( $savePosition / 2 + 1 ))
        echo "Continue from previous save point, $decodedPosition/$linkTotal? (Y/n)"
        read val
        if [ $val != 'n' ]
            then
            linkNum=$savePosition
            iteration=$(( $linkNum / 2 + 1 ))
        fi
    fi

    option='n'
    while [ $option != 'x' ]
        do
        clear
        cat bin/splash.f
        echo "Enter 'x' to exit"
        echo "Enter any other key to continue"
        echo "Opening link $iteration of $linkTotal"
        linkName=$(( $linkNum + 1 ))
        head -n $linkName bin/FilteredOutput.txt | tail -1
        hyperLink=$( head -n $linkNum bin/FilteredOutput.txt | tail -1 )
        firefox $hyperLink &
        echo $linkNum > bin/save_loc.txt
        #editing
        linkNum=$(( $linkNum + 2 ))
        iteration=$(( $iteration + 1 ))
        read option
        done
}

function Menu(){
    cat bin/splash.f
    echo
    echo "1 - Run"
    echo "2 - Clear bin/"
    echo "3 - Regenerate output from bulk data"
    echo "4 - Read output by page"
    echo "5 - Exit"
    echo
    echo -n "Option: "
    read input
}

sleep 1
clear
Menu
while [ $input ]
    do
    case $input in
        1)
            Execute
        ;;
        2)
            Clear
        ;;
        3)
            Regenerate
        ;;
        4)
            readOutput
        ;;
        5)
            echo Exiting...
            exit 0
        ;;
    esac
    Menu
    done