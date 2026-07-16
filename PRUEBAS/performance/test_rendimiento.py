"""
Pruebas de rendimiento: carga, estrés y picos.
Utiliza requests + concurrent.futures para simular concurrencia.
"""
import time
import statistics
import concurrent.futures
import requests
from config import BASE_URL


def measure_get(url):
    start = time.perf_counter()
    try:
        r = requests.get(url, timeout=10)
        elapsed = time.perf_counter() - start
        return {
            "status": r.status_code,
            "elapsed": elapsed,
            "error": None,
        }
    except Exception as e:
        return {
            "status": None,
            "elapsed": time.perf_counter() - start,
            "error": str(e),
        }


def run_scenario(name, url, users, requests_count):
    print(f"\n=== {name} ===")
    results = []
    with concurrent.futures.ThreadPoolExecutor(max_workers=users) as executor:
        futures = [executor.submit(measure_get, url) for _ in range(requests_count)]
        for future in concurrent.futures.as_completed(futures):
            results.append(future.result())

    ok = [r for r in results if r["error"] is None]
    errors = [r for r in results if r["error"] is not None]
    times = [r["elapsed"] for r in ok]

    print(f"Usuarios: {users}, Peticiones: {requests_count}")
    print(f"Exitosas: {len(ok)}, Errores: {len(errors)}")
    if times:
        print(f"Min: {min(times):.3f}s, Max: {max(times):.3f}s, Avg: {statistics.mean(times):.3f}s, Median: {statistics.median(times):.3f}s")
    return results


if __name__ == "__main__":
    login_url = f"{BASE_URL}/login"
    run_scenario("Prueba de carga", login_url, 10, 100)
    run_scenario("Prueba de estrés", login_url, 50, 500)
    run_scenario("Prueba de picos", login_url, 100, 300)
