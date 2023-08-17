import sqlite3

conn = sqlite3.connect("boards.db")
cursor = conn.cursor()

cursor.execute('SELECT * FROM generic_boards')
rows = cursor.fetchall()
[print(row) for row in rows]