# Four Byte decoder

## Description
This library helps to decode Web3 transaction input, where the first 4 bytes from the hex input consists of the called function signature executed in the smart contract.
The hex signatures stored in the `signatures/function` directory, are extracted from https://www.4byte.directory/.
gzip -
If the searched signature is not found locally, a http call is made 4byte and saved, in case the received result is not empty.

From space considerations, in the `signatures/function` folder only the most used 100 signatures are stored, but the
repo contains 816k hex signatures, that are archived in the `function.part.zip`.
If you are considering to use this library to decode a large amount of function signatures, please unzip te function.zip archive into `src/signatures/`.
### How to unzip the multipart zip archive
```bash
# compile the archive parts to a full file
zip -FF function.part.zip --out function.zip
# unzip the full file
unzip functions.zip
```
## Installation
### Via composer
*  Add the following code block to your composer.json (please do not duplicate the repositories section in the composer.json)
```json
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "bogdra/four-byte-decoder",
                "version": "1.0",
                "source": {
                    "url": "https://github.com/bogdra/four-bytes-decoder.git",
                    "type": "git",
                    "reference": "origin/main"
                }
            }
        }
    ],
```
* Run the following command `composer require bogdra/four-byte-decoder`
