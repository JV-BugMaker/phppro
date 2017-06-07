#!/bin/bash
function scandir {
    local cur_dir parent_dir workdir  
    workdir=$1
    cd ${workdir}  
    if [ ${workdir} = "/" ]  
    then  
        cur_dir=""  
    else  
        cur_dir=$(pwd)  
    fi  
   
    for dirlist in $(ls ${cur_dir})  
    do  
        if test -d ${dirlist};then  
            cd ${dirlist} 
            `scandir ${cur_dir}/${dirlist}`
            cd ..  
        else  
            #echo ${cur_dir}/${dirlist}  
            #做自己的工作  
            local filename=$dirlist  
            #echo "当前文件是："$filename  
            #echo ${#2} #.zip 4  
            #echo ${filename:(-${#2})}  
            if [[ ${filename##*.} = 'php' ]]   
            then    
                syntaxCheckRs=`git diff --name-only | grep 'php' |cat | xargs -L php -l`
                if [[ $syntaxCheckRs == *"error"* ]]
                then
                    echo "";
                    echo "syntax check error";
                    #echo $syntaxCheckRs;
                    #echo $filename;
                    exit;
                    break
                fi
            fi
        fi  
    done  
}

function addCheck {
    files=`git diff --name-only | grep 'php'`
    for file in $files
    do
        syntaxCheckRs=`php -l $file`
        if [[ $syntaxCheckRs == *"Fatal"* ]]
        then
        echo "";
        echo "syntax check error";
        echo $syntaxCheckRs;
        exit;
        break
        fi
    done
    if [[ $syntaxCheckRs == *"Fatal"* ]]
    then
    echo "";
    echo "syntax check error";
    echo $syntaxCheckRs;
    exit;
    break
    fi
}
MYDIR=`pwd`
`addCheck`