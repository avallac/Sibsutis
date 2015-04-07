#include "libLab2.h"

int error;

void badResult(int test) {
    error++;
    printf("Error on test #%d\n", test);
}

int main(){
    sc_memoryInit();
    int tmp,tmp2;
    error = 0;
    // Check sc_memorySet
    if (sc_memorySet(-1, 1)) badResult(1);
    if (sc_memorySet(100, 1)) badResult(2);
    if (!sc_memorySet(1, 9)) badResult(3);
     // Check sc_memoryGet
     tmp = 7;
    if (sc_memoryGet(-1, &tmp)) badResult(4);
    if (tmp != 7) badResult(4);
    if (sc_memoryGet(100, &tmp)) badResult(5);
    if (tmp != 7) badResult(5);
    if (!sc_memoryGet(1, &tmp)) badResult(6);
    if (tmp != 9) badResult(6);
    // Check sc_memoryInit
    sc_memoryInit();
    sc_memoryGet(1, &tmp);
    if (tmp != 0) badResult(8);
    sc_memorySet(1, 9);
    // Check sc_memorySave/sc_memoryLoad
    sc_memorySave("dump.tmp");
    sc_memoryInit();
    sc_memoryLoad("dump.tmp");
    sc_memoryGet(1, &tmp);
    if (tmp != 9) badResult(9);

    // =============================

    sc_regInit();
    // Check sc_regSet
    if (sc_regSet(1, -1)) badResult(10);
    if (sc_regSet(1, 2)) badResult(11);
    if (!sc_regSet(SC_F_OM, 1)) badResult(12);
    if (!sc_regSet(SC_F_IG | SC_F_OV, 1)) badResult(13);
    if (sc_regSet(-1, 1)) badResult(14);
    if (sc_regSet(32, 1)) badResult(15);

    // Check sc_regGet
    tmp = 9;
    if (sc_regGet(-1, &tmp)) badResult(16);
    if (tmp != 9) badResult(16);
    if (sc_regGet(32, &tmp)) badResult(17);
    if (tmp != 9) badResult(17);
    if (!sc_regGet(SC_F_OM, &tmp)) badResult(18);
    if (tmp != 1) badResult(18);
    if (!sc_regGet(SC_F_IG | SC_F_OV, &tmp)) badResult(19);
    if (tmp != 1) badResult(19);
    if (!sc_regGet(SC_F_DZ, &tmp)) badResult(20);
    if (tmp != 0) badResult(20);
    sc_regInit();
    if (!sc_regGet(SC_F_OM, &tmp)) badResult(21);
    if (tmp != 0) badResult(21);


    // =============================

    // Check sc_commandEncode
    tmp = 11;
    if (sc_commandEncode(10, -1, &tmp)) badResult(22);
    if (tmp != 11) badResult(22);
    if (sc_commandEncode(10, 128, &tmp)) badResult(23);
    if (tmp != 11) badResult(23);
    if (sc_commandEncode(9, 1, &tmp)) badResult(24);
    if (tmp != 11) badResult(24);
    if (!sc_commandEncode(10, 1, &tmp)) badResult(25);
    if (tmp != 17665) badResult(25);

    // Check sc_commandDecode
    tmp = 11;
    tmp2 = 3;
    if (sc_commandDecode(1, &tmp, &tmp2)) badResult(26);
    if (tmp != 11) badResult(26);
    sc_regGet(SC_F_UN, &tmp);
    if (tmp != 1) badResult(26);
    sc_regInit();
    tmp = 11;
    if (sc_commandDecode(16384, &tmp, &tmp2)) badResult(27);
    if (tmp != 11) badResult(27);
    sc_regGet(SC_F_UN, &tmp);
    if (tmp != 1) badResult(27);
    if (!sc_commandDecode(17665, &tmp, &tmp2)) badResult(28);
    if (tmp != 10) badResult(28);
    if (tmp2 != 1) badResult(28);

    if (error) {
        printf("Tests complete: %d errors\n", error);
    } else {
        printf("Tests complete: without errors\n");
    }
    return 0;
}
