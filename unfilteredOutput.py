from bs4 import BeautifulSoup as bs

#We have 20 index files
for i in range(4):
    with open("bin/index{}.html".format(i + 1), 'r', encoding='UTF-8') as inputFile:
        content = inputFile.read()
        soup = bs(content, 'lxml')
        tags = soup.find_all('a', class_='s-item__link')
        itemList = []
        for tag in tags:
            itemList.append(tag)
    
    filterKeywords = ["ic", "Lock", "Cracked", "Crack", "CRACKED", "LCD", "Locked", "SCREEN", "Screen", "LCD/WORN", "KEYS", "Keys", "2010","2011", "2012", "2013", "2014", "2015", "battery"]
    #The first filtered heading does not appear to contain any useful information
    itemList = itemList[1:]
    print("Length of item list: {}".format(len(itemList)))
    #print(len(itemList))
    #removeItem = 0
    #for item in itemList:
    #    tokenList = str(item.text).split(" ")
    #    for i in tokenList:
    #        if i in filterKeywords:
    #            removeItem = 1
    #    if(removeItem == 1):
    #        itemList.remove(item)
    #       removeItem = 0

    with open("UnfilteredOutput.txt", 'a', encoding='UTF-8') as output:
        for item in itemList:
            output.write(str(item)[425:].split(" ")[0])
            output.write("\n")