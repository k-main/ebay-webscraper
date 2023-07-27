# storeItem_class.py

class storeItem:
    def __init__(self, rawData):

        self.rawData = rawData
        self.itemName = ''
        self.itemLink = ''
        self.itemModel = 'null'

    def getItemName(self):

        self.getItemTokens()
        self.itemName = " ".join(self.tokenList)
        self.itemName = self.itemName[12:-9] #extra fat from styling changes stripped off, editing token list was impacting filter
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

    def getItemModel(self):
        itemTokens = self.getItemTokens()
        for token in itemTokens:
            if (len(token) > 4 and token[0] == "a" and token[1] != "p"):
                self.itemModel = token[0:5]
                break

        return self.itemModel