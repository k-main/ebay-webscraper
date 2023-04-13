#!/bin/bash
function Menu()
{
echo "==============Ebay Webscraper=============="
echo
echo "1 - Retrieve"
echo "2 - Clear bin/"
echo "3 - Regenerate output from bulk data"
echo "4 - Read output by page"
echo "5 - Exit"
echo
echo -n "Option: "
read input
}

Menu
while [ $input ]
    do
    case $input in
        1)
            echo Retrieving bulk data via wget...
            echo Clearing FilteredOutput.txt...
            > FilteredOutput.txt
            echo "Progress 1/4"
            pageNum=1
            tempReset=0
            while [ $pageNum -le 4 ];
                do 
                    clear
                    #echo Emulating Process
                    echo Retrieving bulk data via wget...
                    echo "Progress $pageNum/4"
                    lineNum=$( head -n $pageNum wget-links.txt | tail -1 )
                    echo -n "Retrieving $pageNum/4: $lineNum"
                    #sleep .1
                    wget -q -O index$pageNum.html $lineNum
                    head -n $pageNum wget-links.txt | tail -1 >> wget-temp.txt
                    pageNum=$(( $pageNum + 1 ))
                    tempReset=$(( $tempReset + 1))
                done
                #> wget-temp.txt
                echo "... Done"
                echo "Pushing output to bin... Done"
                mv index* bin/
                echo "Filtering... Done"
                python3 stringProcessor.py
                echo "Outputting links to FilteredOutput.txt... Done"
                echo
        ;;
        2)
            echo -n Clearing bin... 
            rm bin/* 2>> error_log.txt
            if [ $? == 0 ]
                then
                    echo OK.

                else
                    echo Failed: Nothing to clear.
            fi
            #echo done.
            echo
        ;;
        3)
            > FilteredOutput.txt
            python3 stringProcessor.py
            echo
        ;;
        4)
            option='n'
            linkNum=1
            linkTotal=$( wc -l FilteredOutput.txt | cut -d " " -f 1 )

            while [ $option != 'x' ]
                do
                #clear
                echo "Enter 'x' to exit"
                echo "Enter any other key to continue"
                echo "Opening link $linkNum of $linkTotal"
                hyperLink=$( head -n $linkNum FilteredOutput.txt | tail -1 )
                opera $hyperLink
                linkNum=$(( $linkNum + 1 ))
                read option
                done
        ;;
        5)
            echo Exiting...
            exit 0
        ;;
    esac
    #clear
    Menu
    done
