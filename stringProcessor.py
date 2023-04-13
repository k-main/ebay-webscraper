from bs4 import BeautifulSoup as bs

#We have 20 index files
postFilter=0
preFilter=0
for i in range(4):
    with open("bin/index{}.html".format(i + 1), 'r', encoding='UTF-8') as inputFile:
        content = inputFile.read()
        soup = bs(content, 'lxml')
        tags = soup.find_all('a', class_='s-item__link')
        itemList = []
        for tag in tags:
            itemList.append(tag)
    
    '''
    filterKeywords = ["locked", "A1286", "Duo", "Mid-2015", "Mid-2009", "IC", 
                      "HDD", "LCD", "Chassis", "ic", "Lock", "Cracked", 
                      "Lock", "Crack", "CRACKED", "LCD", "Locked", "SCREEN", 
                      "Screen", "LCD/WORN", "KEYS", "Keys", "2010",
                      "2011", "2012", "2013", "2014", "2015","Mid-2013", 
                      "Mid-2014","Mid-2015", "battery", "Mid-2012",
                      'battery', 'A1278', "ic-locked"
                      "(2011)"]
    '''
    
    filteredKeywords = ["locked", "a1286", "duo", "mid-2015", "mid-2009", "ic",
                        "hdd", "lcd", "*lcd", "lcd*", "crack", "cracked", 
                        "screen", "2010", "2011", "2012", "2013", "2014",
                        "2015", "mid-2013", "battery", "ic-locked", "a1278",
                        "mid-2014", "2015,", "locked", "mid-2010", "mid-2010,",
                        "locked,", "activation", "locked.", "keys", "chassis"]
    #The first filtered heading does not appear to contain any useful information
    itemList = itemList[1:]

    #print("Length of item list pre-filter: {}".format(len(itemList)))
    preFilter+=len(itemList)
    #print(len(itemList))
    
    #Increase range back to 4 when ur done with this please 
    itemDeleted=0
    for item in itemList:
        itemDeleted=0
        tokenList=str(item).lower().split(" ")[9:-7]
        tokenList[0] = tokenList[0][15:]
        tokenList[(len(tokenList)-1)] = tokenList[(len(tokenList)-1)][:-18]
        for i in tokenList:
            if((len(i) > 1) and (i[0] == '(')):
                i = i[1:]
                if (i[len(i)-1] == ')'):
                    i = i[-1]

            #print(i)
            if i in filteredKeywords:
                itemList.remove(item)
                itemDeleted=1
                break
        if(itemDeleted==0):
            for i in tokenList:
                print(i)
            
    #print(tokenList)
    #print(str(itemList[0]).split(" ")[9:-7])
    #rows = [str(itemList[i]).split("tab</span></a>") for i in range(len(itemList))]
    
    postFilter+=len(itemList)
    
    #print("Length of item list post-filter: {}".format(len(itemList)))
    with open("FilteredOutput.txt", 'a', encoding='UTF-8') as output:
        for item in itemList:
            linkStr=str(item)[425:].split(" ")[0]
            if(linkStr[0] == '"'):
                linkStr = linkStr[1:-1]
            else:
                linkStr = linkStr[:-1]

            output.write(linkStr)
            #output.write(str(item)[425:].split(" ")[0])
            output.write("\n")

print("Unfiltered item list length: {}".format(preFilter))
print("Filtered list length: {}".format(postFilter))
print("Filtered {} items".format(preFilter - postFilter))