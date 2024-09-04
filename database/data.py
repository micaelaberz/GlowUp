import requests
import sqlite3

import requests
from bs4 import BeautifulSoup

# URL de la página del catálogo de productos
url = 'https://www.farmacity.com/dermocosmetica'

# Hacer la solicitud HTTP
response = requests.get(url)

# Analizar el HTML de la página
soup = BeautifulSoup(response.text, 'html.parser')

# Extraer los productos (esto varía según la estructura HTML del sitio)
productos = soup.find_all('div', class_='product-card')

for producto in productos:
    nombre = producto.find('h2').text
    precio = producto.find('span', class_='price').text
    print(f'Producto: {nombre}, Precio: {precio}')
# def db_setup():
#     connection = sqlite3.connect("library.db")
#     cursor = connection.cursor()
#
#     cursor.execute("""CREATE TABLE IF NOT EXISTS books (
#         isbn TEXT PRIMARY KEY,
#         title TEXT NOT NULL,
#         author TEXT NOT NULL,
#         publisher TEXT NOT NULL,
#         publish_date DATE NOT NULL
#     )""")
#
#     connection.commit()
#     connection.close()
#
#
#
#
# def get_book(isbn):
#     response = requests.get(f"http://openlibrary.org/api/books?bibkeys=ISBN:{isbn}&jscmd=details&format=json")
#     resp_json = response.json()
#
#     if response.status_code != 200 or len(resp_json) == 0:
#         return None
#     else:
#         title = resp_json[f'ISBN:{isbn}']['details']['title']
#         author = resp_json[f'ISBN:{isbn}']['details']['authors'][0]['name']
#         publisher = resp_json[f'ISBN:{isbn}']['details']['publishers'][0]
#         date = resp_json[f'ISBN:{isbn}']['details']['publish_date']
#
#         book_object = book.Book(title, author, publisher, date)
#
#         return book_object
#         # print(resp_json)
#
#
# def add_book(isbn):
#     book_obj = get_book(isbn)
#     if book_obj is not None:
#         connection = sqlite3.connect("library.db")
#         cursor = connection.cursor()
#
#         cursor.execute("SELECT * FROM books WHERE isbn=?", (isbn,))
#
#         book_exists = cursor.fetchone()
#
#         if book_exists is None:
#             cursor.execute("INSERT INTO books (isbn, title, author, publisher, publish_date) VALUES (?, ?, ?, ?, ?)",
#                            (isbn, book_obj.title, book_obj.author, book_obj.publisher, book_obj.date))
#             connection.commit()
#             connection.close()
#             return True
#
#         connection.close()
#     return False
#
#
# def remove_book(isbn):
#     connection = sqlite3.connect("library.db")
#     cursor = connection.cursor()
#
#     cursor.execute("DELETE FROM books WHERE isbn=?", (isbn,))
#
#     connection.commit()
#     connection.close()
#
#
# def all_books():
#     connection = sqlite3.connect("library.db")
#     cursor = connection.cursor()
#
#     cursor.execute("SELECT * FROM books")
#     books = cursor.fetchall()
#
#     connection.close()
#     return books
#
#
# def list_books_by_author(author):
#     connection = sqlite3.connect("library.db")
#     cursor = connection.cursor()
#
#     cursor.execute("SELECT title FROM books WHERE author=?", (author,))
#     books = cursor.fetchall()
#
#     cursor.close()
#     connection.close()
#
#     if books:
#             return books
#     else:
#         return None
#
#
# def main_terminal_ui():
#     db_setup()
#
#     print("Welcome to our Library!")
#     print("~~~~~~~~~~~~~~~~~~~~~~~")
#     while True:
#         print("Please choose an option:\n")
#         print("1. Add Book")
#         print("2. Remove Book")
#         print("3. View Available Books")
#         print("4. Exit\n")
#
#         user_input = input("Your choice: ")
#
#         if user_input == "1":
#             isbn = input("Enter ISBN: ")
#             if add_book(isbn) is False:
#                 print("isbn not found or already exists")
#             else:
#                 print("Book added successfully!\n")
#         elif user_input == "2":
#             isbn = input("Enter ISBN: ")
#             remove_book(isbn)
#             print("Book removed successfully!\n")
#         elif user_input == "3":
#             books = all_books()
#             for book in books:
#                 print(book)
#         elif user_input == "4":
#             print("Thank you for using our Library!")
#             exit()
#         else:
#             print("Invalid input. Please choose a valid option.\n")
#
#
# main_terminal_ui()
