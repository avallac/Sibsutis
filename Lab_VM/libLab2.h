#include <stdio.h>

#define SC_F_OM 1
#define SC_F_IG 2
#define SC_F_DZ 4
#define SC_F_OV 8
#define SC_F_UN 16

int sc_memoryInit();
int sc_memorySet(int address, int value);
int sc_memoryGet(int address, int * value);
int sc_memorySave (char * filename);
int sc_memoryLoad (char * filename);
int sc_regInit (void);
int sc_regSet (int reg, int value);
int sc_regGet (int reg, int * value);
int sc_commandEncode (int command, int operand, int * value);
int sc_commandDecode (int value, int * command, int * operand);
