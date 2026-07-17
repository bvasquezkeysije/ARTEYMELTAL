"""
Prueba de estrés del sistema ARTE Y METAL.
Incrementa la carga a 30 usuarios concurrentes y 150 peticiones.
Target: endpoint /login
"""
import time
import statistics
import concurrent.futures
import requests
from config import BASE_URL


LOGIN_URL = f"{BASE_URL}/login"
USERS = 30
TOTAL_REQUESTS = 150


def measure_get(url, timeout=10):
    start = time.perf_counter()
    try:
        r = requests.get(url, timeout=timeout)
        elapsed = time.perf_counter() - start
        return {"status": r.status_code, "elapsed": elapsed, "error": None}
    except Exception as e:
        return {"status": None, "elapsed": time.perf_counter() - start, "error": str(e)}


def main():
    print("=== PRUEBA DE ESTRÉS ===")
    print(f"Usuarios concurrentes: {USERS}")
    print(f"Total de peticiones: {TOTAL_REQUESTS}")
    print(f"Target: {LOGIN_URL}\n")

    results = []
    with concurrent.futures.ThreadPoolExecutor(max_workers=USERS) as executor:
        futures = [executor.submit(measure_get, LOGIN_URL, 10) for _ in range(TOTAL_REQUESTS)]
        for future in concurrent.futures.as_completed(futures):
            results.append(future.result())

    ok = [r for r in results if r["error"] is None]
    errors = [r for r in results if r["error"] is not None]
    times = [r["elapsed"] for r in ok]
    total_time = sum(times) if times else 0
    throughput = len(ok) / total_time if total_time > 0 else 0

    print(f"Exitosas: {len(ok)}")
    print(f"Errores: {len(errors)}")
    if times:
        print(f"Tiempo mínimo: {min(times):.3f}s")
        print(f"Tiempo máximo: {max(times):.3f}s")
        print(f"Tiempo promedio: {statistics.mean(times):.3f}s")
        print(f"Mediana: {statistics.median(times):.3f}s")
    print(f"Throughput: {throughput:.2f} req/s")
    print(f"Tasa de errores: {len(errors) / len(results) * 100:.2f}%")


if __name__ == "__main__":
    main()
