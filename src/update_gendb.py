from bs4 import BeautifulSoup as bs
from storeItem_class import itemQuery
import sqlite3
import subprocess

#One-time script used to build the generic item database from the text file

conn = sqlite3.connect("boards.db")
cursor = conn.cursor()

def clear_db():
	cursor.execute('DROP TABLE IF EXISTS generic_boards')

def build_db():	
	cursor.execute('DELETE FROM generic_boards')

	with open("boards.txt", 'r', encoding='UTF-8') as generic_boards:
		cursor.execute('CREATE TABLE IF NOT EXISTS generic_boards (id INTEGER PRIMARY KEY, model_num TEXT, year TEXT, type TEXT, board_id TEXT, size TEXT, price TEXT)')
		for board in generic_boards:
			board_arr = board.split(", ")
			cursor.execute('INSERT INTO generic_boards (model_num, year, type, board_id, size, price) VALUES (?, ?, ?, ?, ?, ?)', (board_arr[1], board_arr[2], board_arr[3], board_arr[4], board_arr[5][0:2], "$0") )
	conn.commit()

def search_qry(i_arr):
	i_arr = [str(row) for row in i_arr]
	return 'Macbook+{}+{}+{}"'.format(i_arr[3], i_arr[2], i_arr[5])

def get_price(s_query):
	#under construction
	link_prefix = "https://www.ebay.com/sh/research?marketplace=EBAY-US&keywords="
	link_suffix = "&categoryId=111422&conditionId=3000&offset=0&limit=50&tabName=SOLD&tz=America%2FLos_Angeles"
	return "{}{}{}".format(link_prefix, s_query, link_suffix)
	#subprocess.run(["wget", link])

def update_prices():
	price_dict = dict()
	cursor.execute('SELECT * FROM  {}'.format("generic_boards"))
	rows = cursor.fetchall()
	#[print(search_qry(row)) for row in rows]
	
	for row in rows:
		if (search_qry(row) in price_dict):
			print("Exists")
		else:
			print("Creating entry {}".format(search_qry(row)))
			price_dict[search_qry(row)] = get_price(search_qry(row))

'''
prefix = https://www.ebay.com/sh/research?marketplace=EBAY-US&keywords=
keywords = Macbook+Pro+2016+13+inch
??? = &dayRange=30&endDate=1692893210558&startDate=1690301210558
category = &categoryId=111422&conditionId=3000&offset=0&limit=50&tabName=SOLD&tz=America%2FLos_Angeles

'''
#build_db()
update_prices()
#conn.close()
#comm_arr = ["wget", "google.com"]
#subprocess.run(comm_arr)
