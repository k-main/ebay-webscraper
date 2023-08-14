from storeItem_class import storeItem
from bs4 import BeautifulSoup as bs

#This is cool

with open("boards.html", 'r', encoding='UTF-8') as inputFile:
    content = inputFile.read()
    soup = bs(content, 'lxml')
    tags=soup.find_all('div', class_="mw-content-ltr")
    for tag in tags:
        print(str(tag))
    '''
    tags = soup.find_all('span', class_='mw-headline')
    for tag in tags:
        print(str(tag)[30:-7].split(">")[1])
    '''