import sys, json

args = json.loads(sys.argv[1]) if len(sys.argv) > 1 else {}
name = args.get("name", "No name")

print(json.dumps({
    "status":  True,
    "message": f"Hello from Python, {name}!"
}))
