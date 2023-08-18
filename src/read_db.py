import sqlite3

conn = sqlite3.connect("boards.db")
cursor = conn.cursor()

#Table names: boards, generic_boards
def read_all(table):
    cursor.execute('SELECT * FROM {}'.format(table))
    rows = cursor.fetchall()
    [print(row) for row in rows]

def find_genericboard():
    return

cursor.execute('SELECT * FROM boards LIMIT 5')
items = cursor.fetchall()
for item in items:

    info_arr = str(item)[2:-3].split(" ")
    print(info_arr)

    if (info_arr[1] != "None"):
        cursor.execute('SELECT * FROM generic_boards WHERE model_num LIKE ? AND year LIKE ?', ('%'+info_arr[1]+'%',
        '%' + info_arr[2] + '%'))
        results = cursor.fetchall()
        [print(result) for result in results]

    print()
#read_all("boards")