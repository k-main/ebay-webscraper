# storeItem_class.py

class storeItemGeneric:

    def __init__(self):
        self.itemName = ''
        self.itemSize = 0
        self.itemYear = 0
        self.itemModel = ''

    def setName(self, name):
        self.itemName = name

    def setSize(self, size):
        self.itemSize = size

    def setYear(self, year):
        self.itemYear = year

    def setModel(self, model):
        self.itemModel = model
    


class storeItem:

    def __init__(self, rawData):

        self.rawData = rawData
        self.itemName = ''
        self.itemLink = ''
        self.itemModel = 'null'
        self.itemYear = 0
        self.type = ''
        self.itemDetails = []
        self.tokenSet = {}
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
        self.tokenList[0] = self.tokenList[0][27:]
        self.tokenList[(len(self.tokenList)-1)] = self.tokenList[(len(self.tokenList)-1)][:-27]
        '''
        for i in self.tokenList:
            if((len(i) > 1) and (i[0] == '(')):
                i = i[1:]
                if (i[len(i)-1] == ')'):
                    i = i[-1]
        '''
        self.tokenSet = set(self.tokenList)
        return self.tokenList

    def getType(self):
        if ("pro" in self.tokenSet):
            self.type = "pro"
        elif ("air" in self.tokenSet):
            self.type = "air"
        
        return self.type

    def getItemDetails(self):
        itemTokens = self.getItemTokens()

        self.itemDetails.append(self.getType())

        for token in itemTokens:
            if (len(self.itemDetails) == 3):
                break

            if (len(token) > 4):

                if (token[0] == "a" and token[1] != "p" and token[1:5].isdigit()):
                    model = token[0:5]
                    self.itemDetails.append(model)
                    self.itemModel = model
                if(token[len(token) - 1] == ")" and token[len(token) - 5:-1].isdigit()):
                    year = token[len(token) - 5:-1]
                    if(int(year) <= 2030):
                        self.itemDetails.append(year)
                        self.itemYear = year

            if(token.isdigit() and len(token) == 4 and int(token) < 2030):
                self.itemDetails.append(token)
                self.itemYear = token


            
        return self.itemDetails