#!/bin/bash
cat splash.f
echo Prerequisites: python3-venv, BeautifulSoup4, lxml
echo Enabling virtual environment..
source env/bin/activate

function Execute(){
    clear
    cat splash.f
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
    cat splash.f
    echo Retrieving bulk data via wget...
    echo Clearing FilteredOutput.txt...
    echo Clearing bin/save_loc.txt...
    > FilteredOutput.txt
    > bin/save_loc.txt
    echo '0' > bin/save_loc.txt
    pageLim=$(( $pageNum + 3 ))
    fileNum=1
    clear
    while [ $pageNum -le $pageLim ];
        do 
            clear
            cat splash.f
            echo Retrieving bulk data via wget...
            lineNum=$( head -n $pageNum wget-links.txt | tail -1 )
            echo -n "Retrieving $fileNum/4: $lineNum"
            wget -q -O index$fileNum.html $lineNum
            pageNum=$(( $pageNum + 1 ))
            fileNum=$(( $fileNum + 1 ))
        done
        echo "Pushing output to bin... Done"
        mv index* bin/
        echo "Filtering... Done"
        python3 stringProcessor.py
        echo "Outputting links to FilteredOutput.txt... Done"
        echo
        clear
}

function Clear(){
    echo -n Clearing bin... 
    rm bin/index* 2>> error_log.txt
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
    if [ -f bin/index1.html ];
        then
        > FilteredOutput.txt
        > bin/save_loc.txt
        echo '0' > bin/save_loc.txt
        python3 stringProcessor.py
        echo
        else
        echo No bulk data to regenerate from.
    fi
}

function readOutput(){
    linkTotal=$( wc -l FilteredOutput.txt | cut -d " " -f 1 )
    linkTotal=$(( $linkTotal / 2 ))
    savePosition=$( cat bin/save_loc.txt )
    iteration=1
    linkNum=1
    if [ $savePosition != '0' ]
        then
        echo "Continue from previous save point, $savePosition/$linkTotal? (Y/n)"
        echo "'x' to return"
        read val
        if [ $val != 'n' ]
            then
            linkNum=$savePosition
        fi
    fi

    option='n'
    while [ $option != 'x' ]
        do
        clear
        cat splash.f
        echo "Enter 'x' to exit"
        echo "Enter any other key to continue"
        echo "Opening link $iteration of $linkTotal"
        linkName=$(( $linkNum + 1 ))
        head -n $linkName FilteredOutput.txt | tail -1
        hyperLink=$( head -n $linkNum FilteredOutput.txt | tail -1 )
        firefox $hyperLink
        echo $linkNum > bin/save_loc.txt
        #editing
        linkNum=$(( $linkNum + 2 ))
        iteration=$(( $iteration + 1 ))
        read option
        done
}

function Menu(){
cat splash.f
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