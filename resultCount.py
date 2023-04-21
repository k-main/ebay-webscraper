from bs4 import BeautifulSoup as bs

with open("bin/index1.html", 'r', encoding='UTF-8') as inputFile:
    content = inputFile.read()
    soup = bs(content, 'lxml')
    tags = soup.find('h1', class_='srp-controls__count-heading')
    print(tags.text)

