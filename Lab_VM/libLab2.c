#include "libLab2.h"

int memory[100];
int flags;

/*
a. int sc_memoryInit () – инициализирует оперативную память Simple Computer, за-
давая всем еѐ ячейкам нулевые значения. В качестве «оперативной памяти» использу-
ется массив целых чисел, определенный статически в рамках библиотеки. Размер мас-
сива равен 100 элементам
*/
int sc_memoryInit() {
    int i;
    for(i=0; i < 100; i ++) {
        memory[i] = 0;
    }
}

/*
b. int sc_memorySet (int address, int value) – задает значение указанной
ячейки памяти как value. Если адрес выходит за допустимые границы, то устанавлива-
ется флаг «выход за границы памяти» и работа функции прекращается с ошибкой;
*/
int sc_memorySet(int address, int value) {
    if(address < 0 || address >= 100) {
        sc_regSet(SC_F_OM, 1);
        return 0;
    }
    memory[address] = value;
    return 1;
}

/*
c. int sc_memoryGet (int address, int * value) – возвращает значение указан-
ной ячейки памяти в value. Если адрес выходит за допустимые границы, то устанавли-
вается флаг «выход за границы памяти» и работа функции прекращается с ошибкой.
Значение value в этом случае не изменяется.
*/
int sc_memoryGet(int address, int * value) {
    if(address < 0 || address >= 100) {
        sc_regSet(SC_F_OM, 1);
        return 0;
    }
    *value = memory[address];
    return 1;
}

/*
d. int sc_memorySave (char * filename) – сохраняет содержимое памяти в файл в
бинарном виде (используя функцию write или fwrite);
*/
int sc_memorySave (char * filename) {
    FILE *output = NULL;
    output = fopen(filename, "wb");
    if(output == NULL) return;
    fwrite(memory, sizeof(int), 100, output);
    fclose(output);
}

/*
e. int sc_memoryLoad (char * filename) – загружает из указанного файла содер-
жимое оперативной памяти (используя функцию read или fread);
*/
int sc_memoryLoad (char * filename) {
    FILE *input = NULL;
    input = fopen(filename, "rb");
    if(input == NULL) return;
    fread(memory, sizeof(int), 100, input);
    fclose(input);
}

/*
f. int sc_regInit (void) – инициализирует регистр флагов нулевым значением;
*/
int sc_regInit (void) {
    flags = 0;
}

/*
g. int sc_regSet (int register, int value) – устанавливает значение указанно-
го регистра флагов. Для номеров регистров флагов должны использоваться маски, за-
даваемые макросами (#define). Если указан недопустимый номер регистра или некор-
ректное значение, то функция завершается с ошибкой
*/
int sc_regSet (int reg, int value) {
    if(value!=0 && value !=1) {
        return 0;
    }
    if(reg > 31 || reg <= 0) {
        return 0;
    }
    if (value) {
        flags |= reg;
    } else {
        flags &= !reg;
    }
    return 1;
}

/*
h. int sc_regGet (int register, int * value) – возвращает значение указанного
флага. Если указан недопустимый номер регистра, то функция завершается с ошибкой.
*/
int sc_regGet (int reg, int * value) {
    if(reg > 31 || reg <= 0) {
        return 0;
    }
    if ((flags & reg) == reg) {
        *value = 1;
    } else {
        *value = 0;
    }
    return 1;
}


int sc_checkCommand(int command) {
   if (
        command!=10 &&
        command!=11 &&
        command!=20 &&
        command!=21 &&
        command!=30 &&
        command!=31 &&
        command!=32 &&
        command!=33 &&
        command!=40 &&
        command!=41 &&
        command!=42 &&
        command!=43
    ) {
        return 0;
    }
    return 1;
}

/*
i. int sc_commandEncode (int command, int operand, int * value) – кодиру-
ет команду с указанным номером и операндом и помещает результат в value. Если ука-
заны неправильные значения для команды или операнда, то функция завершается с
ошибкой. В этом случае значение value не изменяется.
*/
int sc_commandEncode (int command, int operand, int * value) {
    if (operand > 127 || operand <= 0) {
        return 0;
    }
    if (!sc_checkCommand(command)) {
        return 0;
    }
    *value = (command << 7) | operand;
    *value |= 16384;
    return 1;
}

/*
j. int sc_commandDecode (int value, int * command, int * operand) – деко-
дирует значение как команду Simple Computer. Если декодирование невозможно, то
устанавливается флаг «ошибочная команда» и функция завершается с ошибкой.
*/
int sc_commandDecode (int value, int * command, int * operand) {
    if (value > 32767 || value < 0) {
        sc_regSet(SC_F_UN, 1);
        return 0;
    }
    if (!(value & 16384)) {
        sc_regSet(SC_F_UN, 1);
        return 0;
    }
    value &= 16383;
    if (!sc_checkCommand(value >> 7)) {
        sc_regSet(SC_F_UN, 1);
        return 0;
    }
    *operand = value & 127;
    *command = value >> 7;

    return 1;
}