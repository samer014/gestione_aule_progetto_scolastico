import json

# Leggi i dati dal file JSON
with open('aule_libere.json', 'r', encoding='utf-8') as f:
    data = json.load(f)

# Genera gli INSERT statement normalizzati
inserts = []
for aula, slots in data.items():
    for slot in slots:
        # Suddividi lo slot in giorno e ora
        giorno, ora = slot.split('&')
        
        # Crea la query per ogni slot temporale
        sql = f"""INSERT INTO aule_libere (aula, giorno, ora)
               VALUES ('{aula}', '{giorno}', '{ora}');"""
        
        inserts.append(sql)

# Scrivi le query su file
with open('insert_normalized.sql', 'w', encoding='utf-8') as f:
    f.write('\n'.join(inserts))

print("File SQL generato: insert_normalized.sql")