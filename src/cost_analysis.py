import sqlite3
import subprocess

DB_PATH = "src/boards.db"

c_dir = str(subprocess.run("pwd", capture_output=True, text=True)).split("'")[3][:-2]
if(c_dir[len(c_dir) - 3:] == "src"):
    DB_PATH = "boards.db"

conn = sqlite3.connect(DB_PATH)
cursor = conn.cursor()

#Table names: boards, generic_boards
def read_all(table):
    cursor.execute('SELECT * FROM {}'.format(table))
    rows = cursor.fetchall()
    [print(row) for row in rows]

def det_nonestr(detail):
    if(detail == 'None'):
        return ''
    return detail

def find_genericboards():
    search_return = []
    cursor.execute('SELECT categorization, link FROM boards')
    items = cursor.fetchall()
    for item in items:
        none_arr = [' ', 'None', 'None']
        item_desc = str(item)[2:-3].split(" ")
        item_desc[2] = item_desc[2][:-2]
        
        if (item_desc[1:-1] == none_arr[1:]):
            continue

        sqlcomm_str = 'SELECT * FROM generic_boards WHERE type LIKE ? AND model_num LIKE ? AND year LIKE ?'
        cursor.execute(sqlcomm_str, ('%' + det_nonestr(item_desc[0]) + '%','%' + det_nonestr(item_desc[1]) + '%','%' + det_nonestr(item_desc[2]) + '%'))
        results = cursor.fetchall()

        #print("{} ->".format([item_desc[:-1], item_desc[3][:10]]))
        if(len(results) > 5):
            continue
        else:
            search_return.append([item_desc,  results])

    return search_return


generic_boards = find_genericboards()
#read_all("boards")
conn.close()
