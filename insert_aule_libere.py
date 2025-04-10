import json

# Leggi i dati dal file JSON
with open('aule_libere.json', 'r', encoding='utf-8') as f:
    data = json.load(f)

# Genera gli INSERT statement
inserts = []
for aula, orari in data.items():
    orari_str = ','.join(orari)
    inserts.append(
        f"INSERT INTO aule_libere (aula, lista_orari_disponibili) "
        f"VALUES ('{aula}', '{orari_str}');"
    )

# Scrivi le query su file
with open('insert_aule.sql', 'w', encoding='utf-8') as f:
    f.write('\n'.join(inserts))

print("File SQL generato: insert_aule.sql")