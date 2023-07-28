from bs4 import BeautifulSoup as bs
from storeItem_class import storeItem

filteredKeywords = ["locked", "a1286", "duo", "mid-2015", "mid-2009", "ic",
                    "hdd", "lcd", "*lcd", "lcd*", "crack", "cracked", 
                    "screen", "2010", "2011", "2012", "2013", "2014",
                    "2015", "mid-2013", "battery", "ic-locked", "a1278",
                    "mid-2014", "2015,", "locked", "mid-2010", "mid-2010,",
                    "locked,", "activation", "locked.", "keys", "chassis",
                    "display", "kb", "*screen", "screen*", "*display", "display*",
                    "*cracked*", "2009", "crack"]

fullObjectList = []

postFilter=0
preFilter=0

def getRawList(fIndex):
    with open("bin/index{}.html".format(fIndex + 1), 'r', encoding='UTF-8') as inputFile:
        content = inputFile.read()
        soup = bs(content, 'lxml')
        tags = soup.find_all('a', class_='s-item__link')
        itemList = []
        for tag in tags:
            itemList.append(tag)
    
    #Ignores the first filtered heading
    itemList = itemList[1:]
    return itemList

def getObjList(itemList):
    itemObjList = []
    for item in itemList:
        addToObjList = 1
        itemObj = storeItem(item)
        storeItemTokens = itemObj.getItemTokens()
        for i in storeItemTokens:
            if i in filteredKeywords:
                addToObjList = 0
                itemList.remove(item)
                break
        if (addToObjList == 1):
            itemObjList.append(itemObj)

    return itemObjList
    
def writeOutput(itemObjList):
    with open("FilteredOutput.txt", 'a', encoding='UTF-8') as output:
        for item in itemObjList:
            output.write(item.getItemLink())
            output.write("\n")
            output.write(item.getItemName())
            output.write("\n")
    return

for i in range(4):
    itemList = getRawList(i) #Raw item list creation
    preFilter+=len(itemList)
    itemObjList = getObjList(itemList) #Object list creation
    fullObjectList+=itemObjList
    postFilter+=len(itemObjList)
    writeOutput(itemObjList) #Write to output

print("Unfiltered item list length: {}".format(preFilter))
print("Filtered list length: {}".format(postFilter))
print("Filtered {} items".format(preFilter - postFilter))