from storeItem_class import storeItem
from bs4 import BeautifulSoup as bs

#This is cool

with open("src/boards.html", 'r', encoding='UTF-8') as inputFile:
    content = inputFile.read()
    soup = bs(content, 'lxml')
    data_tables=soup.find_all('table', class_="wikitable")
    for table in data_tables:
        for line in str(table).split("<tr>"):
            item = line.split("<td>")
            item_arr_length = len(item)
            if (item_arr_length == 1):
                new_arr = line.split('">')
                #new_arr = new_arr[2:-1]
                print(new_arr)
                print("Element count: {}".format(len(new_arr)))
                continue
            print(item)
            print("Element count: {}".format(len(item)))
        #print(str(table).split("<tr>"))
    '''
    tags = soup.find_all('span', class_='mw-headline')
    for tag in tags:
        print(str(tag)[30:-7].split(">")[1])
    '''