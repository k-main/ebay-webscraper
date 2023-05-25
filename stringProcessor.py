from bs4 import BeautifulSoup as bs

filteredKeywords = ["locked", "a1286", "duo", "mid-2015", "mid-2009", "ic",
                    "hdd", "lcd", "*lcd", "lcd*", "crack", "cracked", 
                    "screen", "2010", "2011", "2012", "2013", "2014",
                    "2015", "mid-2013", "battery", "ic-locked", "a1278",
                    "mid-2014", "2015,", "locked", "mid-2010", "mid-2010,",
                    "locked,", "activation", "locked.", "keys", "chassis",
                    "display", "kb", "*screen", "screen*", "*display", "display*",
                    "*cracked*"]


class storeItem:
    def __init__(self, rawData):

        self.rawData = rawData
        self.itemName = ''
        self.itemLink = ''

    def getItemName(self):

        self.getItemTokens()
        self.itemName = " ".join(self.tokenList)
        return self.itemName

    def getItemLink(self):
        
        self.itemLink = str(self.rawData)[425:].split(" ")[0]
        if(self.itemLink[0] == '"'):
                self.itemLink = self.itemLink[1:-1]
        else:
                self.itemLink = self.itemLink[:-1]
        return self.itemLink

    def getItemTokens(self):

        self.tokenList=str(self.rawData).lower().split(" ")[9:-7]
        self.tokenList[0] = self.tokenList[0][15:]
        self.tokenList[(len(self.tokenList)-1)] = self.tokenList[(len(self.tokenList)-1)][:-18]
        for i in self.tokenList:
            if((len(i) > 1) and (i[0] == '(')):
                i = i[1:]
                if (i[len(i)-1] == ')'):
                    i = i[-1]

        return self.tokenList


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

    #The first filtered heading does not appear to contain any useful information
    itemList = itemList[1:]

    preFilter+=len(itemList)
    #print(len(itemList))

    itemDeleted=0
    inspectFilterPass=0
    for item in itemList:

        itemObj = storeItem(item)
        storeItemTokens = itemObj.getItemTokens()

        for i in storeItemTokens:
            if i in filteredKeywords:
                itemList.remove(item)
                itemDeleted=1
                break
    
        if(itemDeleted==0 and inspectFilterPass==1):
            #For human readable output of links which bypass the filter
            tokenList = [print(i, end=" ") for i in tokenList]
            print()
            print()
    
    postFilter+=len(itemList)

    itemObjList = []
    for item in itemList:
        itemObj = storeItem(item)
        itemObjList.append(itemObj)

    with open("FilteredOutput.txt", 'a', encoding='UTF-8') as output:
        for item in itemObjList:
            output.write(item.getItemLink())
            output.write("\n")


print("Unfiltered item list length: {}".format(preFilter))
print("Filtered list length: {}".format(postFilter))
print("Filtered {} items".format(preFilter - postFilter))


