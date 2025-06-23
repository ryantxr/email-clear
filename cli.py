import argparse
import re

class SilentParser(argparse.ArgumentParser):
    def error(self, message):
        self.exit(2, f"{self.prog}: error: {message}\n")

def valid_email(value: str) -> str:
    if not re.match(r"^[^@]+@[^@]+\.[^@]+$", value):
        raise argparse.ArgumentTypeError("bad email syntax")
    return value

parser = SilentParser(description="Email Clear CLI")
parser.add_argument("email", type=valid_email, help="Email address to process")
args = parser.parse_args()
print(f"Processing {args.email}")
