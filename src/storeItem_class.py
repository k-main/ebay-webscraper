# storeItem_class.py
    
class storeItem:

    def __init__(self, rawData):

        self.rawData = rawData
        self.itemName = None
        self.itemLink = None
        self.itemModel = None
        self.itemYear = 0
        self.type = None
        self.itemDetails = []
        self.tokenSet = {}
        self.price = None

    def set_price(self, price):
        self.price = price

    def getItemName(self):
        if (self.itemName == None):
            self.itemName = self.setItemName()
            return self.itemName
        else:
            return self.itemName

    def setItemName(self):

        self.setItemTokens()
        self.itemName = " ".join(self.tokenList)
        return self.itemName

    def getItemLink(self):
        if(self.itemLink == None):
            return self.setItemLink()
        else:
            return self.itemLink
            
    def setItemLink(self):
        self.itemLink = str(self.rawData)[425:].split(" ")[0]
        if(self.itemLink[0] == '"'):
                self.itemLink = self.itemLink[1:-1]
        else:
                self.itemLink = self.itemLink[:-1]

        return self.itemLink

    

    def setItemTokens(self):

        self.tokenList=str(self.rawData.text).split(" ")
        self.tokenList = str(self.rawData.text).lower().split(" ")[:-6]
        self.tokenList[(len(self.tokenList) - 1)] = self.tokenList[(len(self.tokenList) - 1)][:-5]

        self.tokenSet = set(self.tokenList)
        return self.tokenList

    def filterTokens(self):
        self.tokenSet.discard("apple")
        self.tokenSet.discard("macbook")
        new_set = []
        [new_set.append(token) for token in self.tokenSet if len(token) >= 3]
        self.tokenList = new_set
        self.tokenSet = set(new_set)
        #self.tokenSet = newSet

    def setType(self):
        if ("pro" in self.tokenSet):
            self.type = "pro"
        elif ("air" in self.tokenSet):
            self.type = "air"
        
        return self.type
    
    def setModel(self):
        for token in self.tokenSet:
            if(token[0] == "a" and token[1].isdigit()):
                token = "A" + token[1:]
                self.itemModel = token[0:5]
                return self.itemModel
    
    def setYear(self):
        for token in self.tokenSet:
            if(token.isdigit()):
                self.itemYear = token
                return self.itemYear
