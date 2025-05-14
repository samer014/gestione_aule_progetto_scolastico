from flask import Flask, jsonify
import csv
from collections import defaultdict

app = Flask(__name__)

def find_free_slots():
    # Leggi il file CSV e organizza i dati
    aula_schedule = defaultdict(lambda: defaultdict(set))
    with open('_select_aa_mapGCodice_aa_mapOraCodice_aa_aulaId_ac_claId_from_at_202503281016 (1).csv', 'r', encoding='utf-8') as f:
        csv_reader = csv.DictReader(f, delimiter=';')
        for row in csv_reader:
            day = row['mapGCodice']
            hour = row['mapOraCodice']
            aula = row['aulaId']
            aula_schedule[aula][day].add(hour)

    # Mappatura giorni e ore
    giorni = {
        '1': 'LUN',
        '2': 'MAR',
        '3': 'MER',
        '4': 'GIO',
        '5': 'VEN'
    }
    tutte_ore = {'01', '02', '03', '04', '05', '06', '07'}

    # Trova le ore libere
    aule_libere = defaultdict(list)
    
    for aula, giorni_prenotati in aula_schedule.items():
        for giorno_num, giorno_nome in giorni.items():
            ore_occupate = giorni_prenotati.get(giorno_num, set())
            ore_libere = tutte_ore - ore_occupate
            for ora in sorted(ore_libere):
                slot = f"{giorno_nome}&{ora}"
                aule_libere[aula].append(slot)
    
    return dict(aule_libere)

@app.route('/aule_libere', methods=['GET'])
def get_aule_libere():
    free_slots = find_free_slots()
    return jsonify(free_slots)

if __name__ == '__main__':
    app.run(debug=True)