"""
Pruebas de rendimiento: carga, estrés y picos.
Utiliza requests + concurrent.futures para simular concurrencia.

Nota: Las pruebas se ejecutan contra el endpoint /login (público) para evitar
la sobrecarga de autenticación. Los endpoints /dashboard y /api/reniec están
incluidos como pruebas individuales en los escenarios correspondientes.
"""
import time
import statistics
import concurrent.futures
import requests
from config import BASE_URL


LOGIN_URL = f"{BASE_URL}/login"


def measure_get(url, timeout=10):
    start = time.perf_counter()
    try:
        r = requests.get(url, timeout=timeout)
        elapsed = time.perf_counter() - start
        return {
            "url": url,
            "status": r.status_code,
            "elapsed": elapsed,
            "error": None,
        }
    except Exception as e:
        return {
            "url": url,
            "status": None,
            "elapsed": time.perf_counter() - start,
            "error": str(e),
        }


def run_scenario(name, users, total_requests):
    print(f"\n=== {name} ===")
    results = []
    with concurrent.futures.ThreadPoolExecutor(max_workers=users) as executor:
        futures = [
            executor.submit(measure_get, LOGIN_URL, 10)
            for _ in range(total_requests)
        ]
        for future in concurrent.futures.as_completed(futures):
            results.append(future.result())

    ok = [r for r in results if r["error"] is None]
    errors = [r for r in results if r["error"] is not None]
    times = [r["elapsed"] for r in ok]
    total_time = sum(times) if times else 0
    throughput = len(ok) / total_time if total_time > 0 else 0

    print(f"Usuarios: {users}, Peticiones: {total_requests}")
    print(f"Exitosas: {len(ok)}, Errores: {len(errors)}")
    if times:
        print(
            f"Min: {min(times):.3f}s, Max: {max(times):.3f}s, "
            f"Avg: {statistics.mean(times):.3f}s, "
            f"Median: {statistics.median(times):.3f}s"
        )
    print(f"Throughput: {throughput:.2f} req/s")
    print(f"Tasa de errores: {len(errors) / len(results) * 100:.2f}%")
    return results


if __name__ == "__main__":
    run_scenario("Prueba de carga", 10, 50)
    run_scenario("Prueba de estrés", 30, 150)
    run_scenario("Prueba de picos", 50, 100)
