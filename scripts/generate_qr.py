#!/usr/bin/env python3
"""
QR Code Generator for Concerto Renúncia — BilhetesMZ
Generates a high-quality PNG with optional logo overlay.

Usage:
    python3 generate_qr.py --payload-b64 <base64> --size <px> [--logo-path <file>]

Output:
    Raw PNG bytes written to stdout. The caller (PHP) reads them directly.
"""

import argparse
import base64
import sys
from io import BytesIO
from pathlib import Path

import qrcode
from qrcode.image.styledpil import StyledPilImage
from qrcode.image.styles.moduledrawers import RoundedModuleDrawer


def _paste_logo(qr_img, logo_path: str, qr_size: int):
    """Paste a logo in the centre of the QR code image."""
    try:
        from PIL import Image

        logo = Image.open(logo_path).convert("RGBA")

        # Logo should occupy ~22% of the QR area (within error-correction budget)
        logo_max = int(qr_size * 0.22)
        logo.thumbnail((logo_max, logo_max), Image.LANCZOS)

        # White rounded backing pad
        pad = 8
        backing_size = (logo.width + pad * 2, logo.height + pad * 2)
        backing = Image.new("RGBA", backing_size, (255, 255, 255, 255))

        # Paste logo onto backing
        backing.paste(logo, (pad, pad), logo)

        # Centre on QR
        qr_rgba = qr_img.convert("RGBA")
        cx = (qr_size - backing.width) // 2
        cy = (qr_size - backing.height) // 2
        qr_rgba.paste(backing, (cx, cy), backing)
        return qr_rgba.convert("RGB")
    except Exception:
        return qr_img


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Generate a styled QR Code PNG from a base64-encoded payload."
    )
    parser.add_argument("--payload-b64", required=True, help="Base64-encoded QR payload")
    parser.add_argument("--size", type=int, default=300, help="Output image size in px")
    parser.add_argument("--logo-path", default="", help="Optional path to a PNG/JPG logo to embed")
    args = parser.parse_args()

    payload = base64.b64decode(args.payload_b64).decode("utf-8")
    size = max(100, min(1200, args.size))

    # box_size: map requested pixel size → qrcode box_size parameter
    box_size = max(4, min(24, size // 29))

    qr = qrcode.QRCode(
        version=None,
        error_correction=qrcode.constants.ERROR_CORRECT_H,  # H = 30% redundancy → logo is safe
        box_size=box_size,
        border=3,
    )
    qr.add_data(payload)
    qr.make(fit=True)

    # Styled QR with rounded modules
    try:
        img = qr.make_image(
            image_factory=StyledPilImage,
            module_drawer=RoundedModuleDrawer(),
            fill_color="#0D0B07",
            back_color="white",
        )
        img = img._img  # unwrap StyledPilImage to get the actual PIL Image
    except Exception:
        # Fallback to plain black-and-white
        img = qr.make_image(fill_color="black", back_color="white")

    # Resize to requested dimensions
    from PIL import Image as PILImage
    img = img.convert("RGB").resize((size, size), PILImage.LANCZOS)

    # Embed logo if provided and file exists
    logo_path = args.logo_path.strip()
    if logo_path and Path(logo_path).is_file():
        img = _paste_logo(img, logo_path, size)

    output = BytesIO()
    img.save(output, format="PNG", optimize=True)
    sys.stdout.buffer.write(output.getvalue())
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
