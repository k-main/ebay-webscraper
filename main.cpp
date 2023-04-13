#include <cstdlib>
#include <iostream>
#include <string>
#include <fstream>

std::string staticPrefix = "https://www.ebay.com/sch/i.html?_from=R40&_nkw=Macbook+pro&_sacat=0&_dmd=2&LH_ItemCondition=7000&_udhi=550&_pgn=";
int pageNum = 1;

int main()
{
    std::ofstream output;
    output.open("wget-links.txt");

    if (output.is_open()){
        std::cout << "Output file opened successfully" << std::endl;
    } else {
        std::cout << "Output file failed to open" << std::endl;
    }
    
    for (unsigned int i = 1; i <= 4; i++ ){
        output << staticPrefix + std::to_string(i) << std::endl;
    }

   return 0; 
}