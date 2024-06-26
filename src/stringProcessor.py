from bs4 import BeautifulSoup as bs
from storeItem_class import storeItem
import subprocess
import sqlite3
import re

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


# old_filter_found = 0

postFilter=0
preFilter=0

def get_rawlist(fIndex):
    with open("{}index{}.html".format(HTML_PATH, fIndex + 1), 'r', encoding='UTF-8') as inputFile:

        content = inputFile.read()
        soup = bs(content, 'lxml')
        item_wrappers = soup.find_all('div', class_='s-item__wrapper clearfix')[1:]
        itemList  = []
        
        for item_wrapper in item_wrappers:
            item = item_wrapper.find('a', class_='s-item__link')
            price = item_wrapper.find('span', class_='s-item__price').text
            img = str(item_wrapper.find('img')).split("src")[1][2:].split('"')[0]

            itemObj = storeItem(item)
            itemObj.set_price(price)
            itemObj.set_image(img)

            itemList.append(itemObj)


    return itemList

def bad_item(input_str):
    filter_keywords = ["lock", "icloud", "ic", "activation",
                       "battery", "lcd", "screen", "crack",
                       "display", "keyboard", "logic"]
    
    for kwd in filter_keywords:
        if (re.search(kwd, input_str, re.IGNORECASE)):
            return True

    return False

def get_objlist(itemObjList):
    filteredList = []
    for item in itemObjList:
        
        rawstr = str(item.rawData.text)
        if (bad_item(rawstr) == True):
            continue

        item.setItemLink()
        item.setItemName()
        filteredList.append(item)

    return filteredList
    
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
    conn = sqlite3.connect(DATABASE_PATH)
    cursor = conn.cursor()

    cursor.execute('DROP TABLE IF EXISTS boards')
    cursor.execute('CREATE TABLE IF NOT EXISTS boards (id INTEGER PRIMARY KEY, item_name TEXT, categorization TEXT, link TEXT, price TEXT, image TEXT)')
    cursor.execute('DELETE FROM boards')
    
    for item in item_list:
        item.itemDetails = item_cat(item)
        cursor.execute('INSERT INTO boards (item_name, categorization, link, price, image) VALUES (?, ?, ?, ?, ?)', (item.itemName, "{} {} {}".format(item.itemDetails[0], item.itemDetails[1], item.itemDetails[2]), item.itemLink, item.price, item.imgLink))
    
    conn.commit()
    conn.close()


for i in range(4):
    itemList = get_rawlist(i)
    preFilter+=len(itemList)
    itemObjList = get_objlist(itemList) #Object list creation, filtering
    fullObjectList+=itemObjList
    postFilter+=len(itemList) - len(itemObjList)
    write_output(itemObjList) #Write to output
    
build_itemdb(fullObjectList)


print("Unfiltered item list length: {}".format(preFilter))
print("Filtered list length: {}".format(postFilter))
print("Filtered {} items / {}% of total items".format((preFilter - postFilter), str(100*(1 - postFilter/preFilter))[0:5]))
