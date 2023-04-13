#!/bin/bash

link=$(head -n 1 FilteredOutput.txt | tail -1)
opera $link
