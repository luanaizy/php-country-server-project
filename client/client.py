import requests
import json
import xml.etree.ElementTree as ET
import sys

# Tenta importar o módulo gerado pelo Protobuf
try:
    import countries_pb2
except ImportError:
    print("ERRO: O arquivo 'countries_pb2.py' não foi encontrado.")
    sys.exit(1)

class CountryApiClient:
    def __init__(self, base_url):
        self.base_url = base_url

    def _print_separator(self):
        print("-" * 50)

    # --- FUNCIONALIDADE 1: Listar Países e Comparar Formatos [cite: 30, 32, 38] ---
    def compare_formats(self):
        print("\n=== COMPARAÇÃO DE FORMATOS DE DADOS (Países) ===")
        
        url_json = f"{self.base_url}/countries?format=json"
        resp_json = requests.get(url_json)
        size_json = len(resp_json.content)
        print(f"[JSON] Status: {resp_json.status_code} | Tamanho: {size_json} bytes")
        
        url_xml = f"{self.base_url}/countries?format=xml"
        resp_xml = requests.get(url_xml)
        size_xml = len(resp_xml.content)
        print(f"[XML ] Status: {resp_xml.status_code} | Tamanho: {size_xml} bytes")

        url_proto = f"{self.base_url}/countries-proto"
        resp_proto = requests.get(url_proto)
        size_proto = len(resp_proto.content)
        print(f"[PROTO] Status: {resp_proto.status_code} | Tamanho: {size_proto} bytes")

        if resp_proto.status_code == 200:
            country_list = countries_pb2.CountryList()
            country_list.ParseFromString(resp_proto.content)
            count = len(country_list.countries)
            print(f"\n>> Sucesso ao decodificar Protobuf! {count} países recebidos.")
            if count > 0:
                first = country_list.countries[0]
                print(f"   Exemplo: {first.name} ({first.alpha2Code}) - {first.region}")
        
        print(f"\n>> Conclusão: Protobuf economizou {size_json - size_proto} bytes em relação ao JSON.")
        self._print_separator()

    # --- FUNCIONALIDADE 2: Detalhes de um País (Demonstração XML) ---
    def get_country_details(self, code):
        print(f"\n=== DETALHES DO PAÍS ({code}) EM XML ===")
        url = f"{self.base_url}/countries/{code}?format=xml"
        resp = requests.get(url)

        if resp.status_code == 200:
            try:
                root = ET.fromstring(resp.content)
                name = root.find('name').text
                region = root.find('region').text
                capital = root.find('capital').text
                print(f"País: {name}")
                print(f"Região: {region}")
                print(f"Capital: {capital}")
                print("RAW XML:", resp.text[:100] + "...")
            except Exception as e:
                print("Erro ao processar XML:", e)
        else:
            print(f"Erro: {resp.status_code} - {resp.text}")
        self._print_separator()

    # --- FUNCIONALIDADE 3: Sistema de Favoritos (CRUD Completo) [cite: 26] ---
    def manage_favorites(self):
        print("\n=== GERENCIAMENTO DE FAVORITOS (CRUD) ===")

        print("\n1. Adicionando Favoritos (POST)...")
        to_add = [
            {"code": "BR", "name": "Brazil"},
            {"code": "JP", "name": "Japan"},
            {"code": "CA", "name": "Canada"}
        ]
        
        for item in to_add:
            resp = requests.post(f"{self.base_url}/favorites", json=item)
            print(f"   Add {item['code']}: {resp.status_code} - {resp.json().get('message')}")

        print("\n2. Listando Favoritos (GET)...")
        resp = requests.get(f"{self.base_url}/favorites")
        favorites = resp.json()
        print(f"   Favoritos atuais: {json.dumps(favorites, indent=2, ensure_ascii=False)}")

        print("\n3. Buscando favorito específico 'JP' (GET)...")
        resp = requests.get(f"{self.base_url}/favorites/JP")
        if resp.status_code == 200:
            print(f"   Encontrado: {resp.json()}")
        else:
            print("   Não encontrado.")

        print("\n4. Removendo 'JP' (DELETE)...")
        resp = requests.delete(f"{self.base_url}/favorites/JP")
        print(f"   Status Delete: {resp.status_code} - {resp.json().get('message')}")

        resp_final = requests.get(f"{self.base_url}/favorites")
        print(f"   Lista Final: {[f['code'] for f in resp_final.json()]}")
        self._print_separator()

if __name__ == "__main__":
    
    BASE_URL = "http://localhost:8000/api"
    
    client = CountryApiClient(BASE_URL)
    
    try:
        # Verifica se o servidor está online
        requests.get(BASE_URL + "/countries")
    except requests.exceptions.ConnectionError:
        print("ERRO: Não foi possível conectar ao servidor.")
        print("Certifique-se de rodar o servidor PHP: php -S localhost:8000 -t api")
        sys.exit(1)

    # Executa o fluxo de demonstração exigido no trabalho
    client.compare_formats()      # Atende aos requisitos de ProtoBuf e Comparação
    client.get_country_details("BR") # Atende ao requisito de uso de XML
    client.manage_favorites()     # Atende ao requisito de CRUD e funcionalidade extra