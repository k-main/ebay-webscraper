# Installation

The program runs within a Python3 virtual environemnt. Run the install.sh script in the root directory of the program to install all of the required dependencies.
If you already have these dependencies installed, the script won't try to install them again. If the install script doesn't have executable permission on your
machine, you can give it executable permissions by executing the following in the root directory of the folder.

`sudo chmod +x install.sh`

You can run the same command for `run.sh` if that script also lacks executable permissions.

## Usage

![preview](https://i.imgur.com/oPdw4D1.png)

Run the main script using `./run.sh`. It will check to make sure all of the dependencies are present before continuing, and will exit with error code `1` otherwise.

### Menu item selection

After the dependency checks pass successfully a small menu with 5 options are displayed, each corresponding to an integer 1-5. To pick an option, enter the corresponding
integer.

#### 1: Run
Run is the first option, after selecting it there are 2 criteria you can select from; Macbook Pros or Macbooks and Macbook Pros. All this does is modify the search
querty to either only search for Macbook Pros, or search for both. 

After this, the script will use wget to fetch about 1000 results directly from eBay, and the `stringProcessor.py` Python program will filter out undesired results
using the an array defined in the script itself. Eventually I will make it so the filter is not hard-coded into the script and can be modified via some configuration
file.

#### 2: Clear bin/
The raw HTML files are stored within the `bin` folder. Selecting this option will clear them.

#### 3: Regenerate output from bulk data
This uses the HTML files stored in `bin` and runs them through `stringProcessor.py` once again. This can be useful if you want to experiment with different filter
criteria.

#### 4: Read output by page.
This extracts the URLs from the results which were not filtered by the python script, and uses firefox to open them so they can be inspected individually.
Typically, hundreds out of the thousand original search results are not filtered, so your position in the list will be saved if the program is closed, and you can
resume searching through the filtered results later on.

#### 5: Exit
Exits the program.






