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
            echo Retrieve:
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
            echo Retrieving bulk data via wget...
            echo Clearing FilteredOutput.txt...
            echo Clearing bin/save_loc.txt...
            > FilteredOutput.txt
            > bin/save_loc.txt
            echo '0' > bin/save_loc.txt
            pageLim=$(( $pageNum + 3 ))
            fileNum=1
            while [ $pageNum -le $pageLim ];
                do 
                    clear
                    #echo Emulating Process
                    echo Retrieving bulk data via wget...
                    lineNum=$( head -n $pageNum wget-links.txt | tail -1 )
                    echo -n "Retrieving $fileNum/4: $lineNum"
                    #sleep .1
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
        ;;
        2)
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
        ;;
        3)
            > FilteredOutput.txt
            > bin/save_loc.txt
            echo '0' > bin/save_loc.txt
            python3 stringProcessor.py
            echo
        ;;
        4)

            linkTotal=$( wc -l FilteredOutput.txt | cut -d " " -f 1 )
            savePosition=$( cat bin/save_loc.txt )

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
                #clear
                echo "Enter 'x' to exit"
                echo "Enter any other key to continue"
                echo "Opening link $linkNum of $linkTotal"
                hyperLink=$( head -n $linkNum FilteredOutput.txt | tail -1 )
                opera $hyperLink
                echo $linkNum > bin/save_loc.txt
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