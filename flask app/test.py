from flask import Flask, render_template
from pdf2image import convert_from_path

app = Flask(__name__)

@app.route('/client/img')
def convert_pdf_to_image():
    pdf_file = 'input.pdf'  # Replace with your PDF file path
    output_prefix = 'output_image'

    images = convert_from_path(pdf_file)

    for i, image in enumerate(images):
        image.save(f'{output_prefix}_{i + 1}.jpg', 'JPEG')

    return 'PDF converted to images successfully!'

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)