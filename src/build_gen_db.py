import sqlite3

#One-time script used to build the generic item database from the text file

conn = sqlite3.connect("boards.db")
cursor = conn.cursor()

cursor.execute('DELETE FROM generic_boards')

with open("boards.txt", 'r', encoding='UTF-8') as generic_boards:
	cursor.execute('CREATE TABLE IF NOT EXISTS generic_boards (id INTEGER PRIMARY KEY, model_num TEXT, year TEXT, type TEXT, board_id TEXT, size TEXT)')
	for board in generic_boards:
		board_arr = board.split(", ")
		cursor.execute('INSERT INTO generic_boards (model_num, year, type, board_id, size) VALUES (?, ?, ?, ?, ?)', (board_arr[1], board_arr[2], board_arr[3], board_arr[4], board_arr[5][0:2]) )

conn.commit()
conn.close()