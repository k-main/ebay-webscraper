from storeItem_class import storeItem
from bs4 import BeautifulSoup as bs

#This is cool

def proc_tokens(item_arr, current_size):
    if (len(item_arr) == 2):
        item_arr_1 = item_arr[0].split('">')
        item_arr_1.append(item_arr[1])
        item_arr = item_arr_1[2:]
        item_arr[0] = item_arr[0][0:8]
        #item_arr[1] = item_arr[1][0:5]
        item_arr[2] = item_arr[2][0:-35]
        item_arr[3] = item_arr[3][7:-7][0:3]
        item_arr[4] = item_arr[4][0:9]
    elif (len(item_arr) == 5):
        item_arr[0] = item_arr[0][0:8]
        #item_arr[1] = item_arr[1][0:5]
        item_arr[2] = item_arr[2][0:-35]
        item_arr[3] = item_arr[3][7:-35][0:3]
        item_arr[4] = item_arr[4][0:9]
    else:
        item_arr = item_arr[2:]
        item_arr[0] = item_arr[0][0:8]
        #item_arr[1] = item_arr[1][0:5]
        item_arr[2] = item_arr[2][0:-7]
        if (item_arr[3][0:7] == "MacBook"):
            item_arr[3] = item_arr[3][7:-11]
        else:
            item_arr[3] = "null"   
        item_arr[4] = item_arr[4][0:9]
        item_arr = item_arr[0:-1]

    if (item_arr[0][0] == "E"):
        item_arr[1] = item_arr[1][0:5]
        item_arr[0].rstrip("\n")
        item_arr[4].rstrip("\n")
        item_arr.append(current_size)
        return item_arr
    else:
        return []


        

with open("src/boards.html", 'r', encoding='UTF-8') as inputFile:
    content = inputFile.read()
    soup = bs(content, 'lxml')
    
    final_arr = []

    data_tables=soup.find_all('table', class_="wikitable")
    current_size = ""
    for table in data_tables:
        for line in str(table).split("<tr>"):
            
            item = line.split("<td>")
            item_arr_length = len(item)
            
            if (item_arr_length == 1 and len(line.split('">')) == 8):

                new_arr = line.split('">')

                if (new_arr[1][0:4] == "<str"):
                    current_size=new_arr[1][8:10]
                
                item = new_arr[2:-1]
                item_arr_length = len(item)
        
            if (item_arr_length == 1):
                continue
            else:
                #print(proc_tokens(item, current_size))
                final_arr.append(proc_tokens(item, current_size))
    
    
    
    with open("src/boards.txt", "a", encoding='UTF-8') as output:
        for item in final_arr:
            output.write(str(item))
            output.write("\n")
    