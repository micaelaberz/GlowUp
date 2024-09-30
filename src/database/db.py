import pypyodbc as odbc
import pandas as pd

DRIVER_NAME='SQL SERVER'
SERVER_NAME='DESKTOP-VHHEB56\SQLEXPRESS'
DATABASE_NAME='GlowUp'

connection_string= f"""
DRIVER={{{DRIVER_NAME}}};
SERVER={SERVER_NAME};
DATABASE={DATABASE_NAME};
Trust_Connection=yes;
"""

conn = odbc.connect(connection_string)
print(conn)


cursor = conn.cursor()

# Verificar si la tabla tiene datos
cursor.execute("SELECT COUNT(*) FROM usuarios")
count = cursor.fetchone()[0]
print(f"Número de registros en usuarios: {count}")

# Ejecutar la consulta
if count > 0:
    cursor.execute("SELECT * FROM usuarios")

    # Obtener los resultados
    rows = cursor.fetchall()

    # Mostrar los resultados
    for row in rows:
        print(row)

# Cerrar el cursor y la conexión
cursor.close()
conn.close()