from bs4 import BeautifulSoup as bs
from storeItem_class import storeItem
import subprocess
import sqlite3


#Wait a min, wouldn't sending over all the finalized data to an sql db
#be better than using a text file? It would be easier to read it off in our
#main script, but wed also have to move over some of the menu logic into a py script


HTML_PATH = "bin/pages/"
OUTPUT_PATH = "bin/FilteredOutput.txt"
DATABASE_PATH = "src/boards.db"

c_dir = str(subprocess.run("pwd", capture_output=True, text=True)).split("'")[3][:-2]
if(c_dir[len(c_dir) - 3:] == "src"):
    HTML_PATH = "../bin/pages/"
    OUTPUT_PATH = "../bin/FilteredOutput.txt"
    DATABASE_PATH = "boards.db"

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

def get_rawlist(fIndex):
    with open("{}index{}.html".format(HTML_PATH, fIndex + 1), 'r', encoding='UTF-8') as inputFile:
        content = inputFile.read()
        soup = bs(content, 'lxml')
        itemTitles = soup.find_all('a', class_='s-item__link')
        itemList = []
        [itemList.append(title) for title in itemTitles]
        itemPrices = soup.find_all('span', class_='s-item__price')
        priceList = []
        [priceList.append(price.text) for price in itemPrices]
        
    
    #Ignores the first filtered heading
    itemList = itemList[1:]
    priceList = priceList[1:]
    return [itemList, priceList]

def get_objlist(itemList, priceList):
    itemObjList = []
    index = 0
    for item in itemList:
        addToObjList = 1
        itemObj = storeItem(item)
        itemObj.price = priceList[index]
        storeItemTokens = itemObj.setItemTokens()
        #print(storeItemTokens, itemObj.price)
        index+=1
        for i in storeItemTokens:
            if i in filteredKeywords:
                addToObjList = 0
                itemList.remove(item)
                break
        if (addToObjList == 1):
            itemObj.setItemLink()
            itemObj.setItemName()
            itemObjList.append(itemObj)
        

    return itemObjList
    
def write_output(itemObjList):
    with open(OUTPUT_PATH, 'a', encoding='UTF-8') as output:
        for item in itemObjList:
            output.write(item.getItemLink())
            output.write("\n")
            output.write(item.getItemName())
            output.write("\n")
    return

def item_cat(item):
    item.filterTokens()
    details = [item.setType(), item.setModel(), item.setYear()]
    return details

def build_itemdb(item_list):
    #[obj.itemDetails = item_cat(item) for item in item_list]
    conn = sqlite3.connect(DATABASE_PATH)
    cursor = conn.cursor()

    cursor.execute('DROP TABLE IF EXISTS boards')
    cursor.execute('CREATE TABLE IF NOT EXISTS boards (id INTEGER PRIMARY KEY, item_name TEXT, categorization TEXT, link TEXT, price TEXT)')
    cursor.execute('DELETE FROM boards')

    for item in item_list:
        item.itemDetails = item_cat(item)
        cursor.execute('INSERT INTO boards (item_name, categorization, link, price) VALUES (?, ?, ?, ?)', (item.itemName, "{} {} {}".format(item.itemDetails[0], item.itemDetails[1], item.itemDetails[2]), item.itemLink, item.price))
    
    conn.commit()

for i in range(4):
    returnArr = get_rawlist(i)
    itemList = returnArr[0] #Raw item list creation
    priceList = returnArr[1]
    preFilter+=len(itemList)
    itemObjList = get_objlist(itemList, priceList) #Object list creation, filtering
    fullObjectList+=itemObjList
    postFilter+=len(itemObjList)
    write_output(itemObjList) #Write to output
    
build_itemdb(fullObjectList)



print("Unfiltered item list length: {}".format(preFilter))
print("Filtered list length: {}".format(postFilter))
print("Filtered {} items".format(preFilter - postFilter))